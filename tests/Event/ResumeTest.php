<?php
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
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Auth\Event;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-06-01 at 18:06:32.
 * @covers \Caridea\Auth\Event\Resume
 * @covers \Caridea\Auth\Event
 */
class ResumeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Caridea\Auth\Event
     * @covers Caridea\Auth\Event\Resume::getFirstActive
     * @covers Caridea\Auth\Event\Resume::getLastActive
     */
    public function testBasic()
    {
        $now = microtime(true);
        $object = new Resume(
            $this,
            \Caridea\Auth\Principal::getAnonymous(),
            $now - 5,
            $now
        );
        $this->assertEquals(\Caridea\Auth\Principal::getAnonymous(), $object->getPrincipal());
        $this->assertEquals($now - 5, $object->getFirstActive());
        $this->assertEquals($now, $object->getLastActive());
    }
}
