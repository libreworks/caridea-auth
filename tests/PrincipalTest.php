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
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Auth;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-06-01 at 18:02:10.
 *
 * @backupStaticAttributes enabled
 */
class PrincipalTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $prop = new \ReflectionProperty(Principal::class, 'anon');
        $prop->setAccessible(true);
        $prop->setValue(null);
    }
    
    /**
     * @covers Caridea\Auth\Principal
     */
    public function testGet()
    {
        $username = 'foobar';
        $details = ['foo' => 'bar'];
        $object = Principal::get($username, $details);
        $this->assertEquals($username, $object->getUsername());
        $this->assertEquals($details, $object->getDetails());
        $this->assertFalse($object->isAnonymous());
        $this->assertEquals($username, (string)$object);
    }

    /**
     * @covers Caridea\Auth\Principal
     */
    public function testGetAnonymous()
    {
        $anon = Principal::getAnonymous();
        $this->assertNull($anon->getUsername());
        $this->assertEmpty($anon->getDetails());
        $this->assertTrue($anon->isAnonymous());
        $this->assertEquals('[anonymous]', (string)$anon);
        $this->assertSame($anon, Principal::getAnonymous());
    }
}
