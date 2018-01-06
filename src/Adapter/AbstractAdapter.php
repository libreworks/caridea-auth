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
 * Abstract authentication adapter.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
abstract class AbstractAdapter implements \Caridea\Auth\Adapter
{
    /**
     * Checks that a string argument isn't null, empty, or just whitespace.
     *
     * @param string $object The value to check for blankness
     * @param string $fieldName The name of the parameter (for Exception message)
     * @return mixed Returns `$object`
     * @throws \InvalidArgumentException if the value is null, empty, or whitespace
     */
    protected function checkBlank($object, string $fieldName)
    {
        if ($object === null || strlen(trim($object)) === 0) {
            throw new \InvalidArgumentException("The \"$fieldName\" argument is required; it cannot be null, empty, nor containing only whitespace");
        }
        return $object;
    }
    
    /**
     * Throws a `MissingCredentials` if the value is empty.
     *
     * @param array $source The params array
     * @param string $key The array offset
     * @return mixed Returns the value of `$source[$key]`
     * @throws \Caridea\Auth\Exception\MissingCredentials If `$source[$key]` is empty
     */
    protected function ensure(array &$source, string $key)
    {
        if (!isset($source[$key]) || !$source[$key]) {
            throw new \Caridea\Auth\Exception\MissingCredentials();
        }
        return $source[$key];
    }
    
    /**
     * Verifies a user-provided password against a hash.
     *
     * @param string $input The user-provided password
     * @param string $hash The stored password hash
     * @throws \Caridea\Auth\Exception\MissingCredentials If the user-provided password is empty
     * @throws \Caridea\Auth\Exception\InvalidPassword If the password fails to verify
     */
    protected function verify(string $input, string $hash)
    {
        if (!password_verify($input, $hash)) {
            throw new \Caridea\Auth\Exception\InvalidPassword();
        }
    }
    
    /**
     * Gets a default set of details for web requests (includes User-Agent and IP).
     *
     * ```php
     * $details = $this->details($request, ['foo' => 'bar'])
     * // $details = [
     * //     'ua' => 'My User Agent/1.0',
     * //     'ip' => '127.0.0.1',
     * //     'foo' => 'bar'
     * // ]
     *
     * ```
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The server request
     * @param array $details Any details to add
     * @return array The details
     */
    protected function details(\Psr\Http\Message\ServerRequestInterface $request, array $details): array
    {
        $server = $request->getServerParams();
        return array_merge([
            'ua' => $server['HTTP_USER_AGENT'],
            'ip' => $server['REMOTE_ADDR']
            ], $details);
    }
}
