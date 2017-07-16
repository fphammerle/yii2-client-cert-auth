<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \yii\db\Connection;

class SqliteTest extends TestCase
{
    public function createConnection($persistent)
    {
        return new Connection([
            'dsn' => 'sqlite::memory:',
            'attributes' => [
                \PDO::ATTR_PERSISTENT => $persistent,
            ],
        ]);
    }

    public function getTableNames(Connection $db)
    {
        return array_map(
            function($tbl) { return $tbl['name']; },
            $db->createCommand('SELECT name FROM sqlite_master')->queryAll()
        );
    }

    public function testPersistence()
    {
        $a = $this->createConnection(false);
        $this->assertEquals([], $this->getTableNames($a));
        $a->createCommand('CREATE TABLE a (aa INT)')->execute();
        $this->assertEquals(['a'], $this->getTableNames($a));

        $b = $this->createConnection(false);
        $this->assertEquals([], $this->getTableNames($b));
        $b->createCommand('CREATE TABLE b (bb INT)')->execute();
        $this->assertEquals(['a'], $this->getTableNames($a));
        $this->assertEquals(['b'], $this->getTableNames($b));

        $c = $this->createConnection(true);
        $this->assertEquals([], $this->getTableNames($c));
        $c->createCommand('CREATE TABLE c (cc INT)')->execute();
        $this->assertEquals(['b'], $this->getTableNames($b));
        $this->assertEquals(['c'], $this->getTableNames($c));

        $d = $this->createConnection(false);
        $this->assertEquals([], $this->getTableNames($d));

        $e = $this->createConnection(true);
        $this->assertEquals(['c'], $this->getTableNames($e));
        $e->createCommand('CREATE TABLE e (ee INT)')->execute();
        $this->assertEquals(['c', 'e'], $this->getTableNames($c));
        $this->assertEquals([], $this->getTableNames($d));
        $this->assertEquals(['c', 'e'], $this->getTableNames($e));
    }

    public function testCopyConnection()
    {
        $a = $this->mockApplication();
        $default_tables = $this->getTableNames($a->db);
        $a->db->createCommand('CREATE TABLE a (aa INT)')->execute();
        $this->assertEquals(
            array_merge($default_tables, ['a']),
            $this->getTableNames($a->db)
        );

        $b = $this->mockApplication();
        $this->assertEquals($default_tables, $this->getTableNames($b->db));

        $c = $this->mockApplication([
            'components' => [
                'db' => $a->db,
            ],
        ]);
        $this->assertEquals(
            array_merge($default_tables, ['a']),
            $this->getTableNames($c->db)
        );

        $c->db->createCommand('CREATE TABLE c (cc INT)')->execute();
        $this->assertEquals(
            array_merge($default_tables, ['a', 'c']),
            $this->getTableNames($c->db)
        );
        $this->assertEquals(
            $this->getTableNames($c->db),
            $this->getTableNames($a->db)
        );
        $this->assertEquals(
            $default_tables,
            $this->getTableNames($b->db)
        );
    }
}
