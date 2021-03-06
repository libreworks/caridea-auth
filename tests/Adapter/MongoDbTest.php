<?php
declare(strict_types=1);
/**
 * Caridea
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Auth\Adapter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-03-21 at 19:00:12.
 *
 * @requires extension mongodb
 */
class MongoDbTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->manager = new \MongoDB\Driver\Manager($this->getUri());
        $this->manager->executeCommand(
            $this->getDatabaseName(),
            new \MongoDB\Driver\Command(array('dropDatabase' => 1))
        );
    }

    /**
     * @covers Caridea\Auth\Adapter\AbstractAdapter
     * @covers Caridea\Auth\Adapter\MongoDb::__construct
     * @covers Caridea\Auth\Adapter\MongoDb::login
     * @covers Caridea\Auth\Adapter\MongoDb::getResults
     * @covers Caridea\Auth\Adapter\MongoDb::fetchResult
     */
    public function testLogin()
    {
        $password = 'correct horse battery staple';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $bulk = new \MongoDB\Driver\BulkWrite();
        $id = new \MongoDB\BSON\ObjectID();
        $bulk->insert(['_id' => $id, 'user' => 'foobar', 'pass' => $hash, 'foo' => 'bar']);
        $wc = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulk, $wc);

        $object = new MongoDb($this->manager, $this->getNamespace(), 'user', 'pass', ['foo' => 'bar']);

        $request = $this->getMockBuilder(\Psr\Http\Message\ServerRequestInterface::class)
            ->setMethods(['getParsedBody'])
            ->getMockForAbstractClass();
        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['username' => 'foobar', 'password' => $password]);

        $auth = $object->login($request);

        $this->assertInstanceOf(\Caridea\Auth\Principal::class, $auth);
        $this->assertEquals('foobar', $auth->getUsername());
        $this->assertEquals((string) $id, $auth->getDetails()['id']);
    }

    /**
     * @covers Caridea\Auth\Adapter\AbstractAdapter
     * @covers Caridea\Auth\Adapter\MongoDb::__construct
     * @covers Caridea\Auth\Adapter\MongoDb::login
     * @covers Caridea\Auth\Adapter\MongoDb::getResults
     * @covers Caridea\Auth\Adapter\MongoDb::fetchResult
     * @covers Caridea\Auth\Exception\InvalidPassword
     * @expectedException \Caridea\Auth\Exception\InvalidPassword
     */
    public function testLoginInvalid()
    {
        $password = 'correct horse battery staple';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->insert(['user' => 'foobar', 'pass' => $hash, 'foo' => 'bar']);
        $wc = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulk, $wc);

        $object = new MongoDb($this->manager, $this->getNamespace(), 'user', 'pass', ['foo' => 'bar']);

        $request = $this->getMockBuilder(\Psr\Http\Message\ServerRequestInterface::class)
            ->setMethods(['getParsedBody'])
            ->getMockForAbstractClass();
        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['username' => 'foobar', 'password' => 'wrong password']);

        $object->login($request);
    }

    /**
     * @covers Caridea\Auth\Adapter\AbstractAdapter
     * @covers Caridea\Auth\Adapter\MongoDb::__construct
     * @covers Caridea\Auth\Adapter\MongoDb::login
     * @covers Caridea\Auth\Adapter\MongoDb::getResults
     * @covers Caridea\Auth\Adapter\MongoDb::fetchResult
     * @covers Caridea\Auth\Exception\UsernameAmbiguous
     * @expectedException \Caridea\Auth\Exception\UsernameAmbiguous
     */
    public function testLoginMulti()
    {
        $password = 'correct horse battery staple';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->insert(['user' => 'foobar', 'pass' => $hash]);
        $bulk->insert(['user' => 'foobar', 'pass' => 'hash2']);
        $wc = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulk, $wc);

        $object = new MongoDb($this->manager, $this->getNamespace(), 'user', 'pass');

        $request = $this->getMockBuilder(\Psr\Http\Message\ServerRequestInterface::class)
            ->setMethods(['getParsedBody'])
            ->getMockForAbstractClass();
        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['username' => 'foobar', 'password' => 'password']);

        $object->login($request);
    }

    /**
     * @covers Caridea\Auth\Adapter\AbstractAdapter
     * @covers Caridea\Auth\Adapter\MongoDb::__construct
     * @covers Caridea\Auth\Adapter\MongoDb::login
     * @covers Caridea\Auth\Adapter\MongoDb::getResults
     * @covers Caridea\Auth\Adapter\MongoDb::fetchResult
     * @covers Caridea\Auth\Exception\UsernameNotFound
     * @expectedException \Caridea\Auth\Exception\UsernameNotFound
     */
    public function testLoginNone()
    {
        $object = new MongoDb($this->manager, $this->getNamespace(), 'user', 'pass');

        $request = $this->getMockBuilder(\Psr\Http\Message\ServerRequestInterface::class)
            ->setMethods(['getParsedBody'])
            ->getMockForAbstractClass();
        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['username' => 'foobar', 'password' => null]);

        $object->login($request);
    }

    /**
     * @covers Caridea\Auth\Adapter\AbstractAdapter
     * @covers Caridea\Auth\Adapter\MongoDb::__construct
     * @covers Caridea\Auth\Adapter\MongoDb::login
     * @covers Caridea\Auth\Adapter\MongoDb::getResults
     * @covers Caridea\Auth\Exception\ConnectionFailed
     * @expectedException \Caridea\Auth\Exception\ConnectionFailed
     * @expectedExceptionMessage Cannot read from source data
     */
    public function testLoginConnection()
    {
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27018");
        $object = new MongoDb($manager, $this->getNamespace(), 'user', 'pass', []);

        $request = $this->getMockBuilder(\Psr\Http\Message\ServerRequestInterface::class)
            ->setMethods(['getParsedBody'])
            ->getMockForAbstractClass();
        $request->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['username' => 'foobar', 'password' => null]);

        $object->login($request);
    }

    /**
     * Return the connection URI.
     *
     * Borrowed from the mongo-php-library project's unit tests
     *
     * @return string
     */
    protected function getUri()
    {
        return getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';
    }

    /**
     * Return the test collection name.
     *
     * Borrowed from the mongo-php-library project's unit tests
     *
     * @return string
     */
    protected function getCollectionName()
    {
         $class = new \ReflectionClass($this);
         return sprintf('%s.%s', $class->getShortName(), hash('crc32b', $this->getName()));
    }

    /**
     * Return the test database name.
     *
     * Borrowed from the mongo-php-library project's unit tests
     *
     * @return string
     */
    protected function getDatabaseName()
    {
        return getenv('MONGODB_DATABASE') ?: 'caridea_test';
    }

    /**
     * Return the test namespace.
     *
     * Borrowed from the mongo-php-library project's unit tests
     *
     * @return string
     */
    protected function getNamespace()
    {
         return sprintf('%s.%s', $this->getDatabaseName(), $this->getCollectionName());
    }
}
