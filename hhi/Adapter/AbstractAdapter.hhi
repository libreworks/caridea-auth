<?hh

namespace Caridea\Auth\Adapter;

abstract class AbstractAdapter implements \Caridea\Auth\Adapter
{
    protected function checkBlank<T>(T $object, string $fieldName): T
    {
        return $object;
    }
    
    protected function ensure<T>(array<string,T> $source, string $key): T
    {
        return $source[$key];
    }

    protected function verify(string $input, string $hash): void
    {
    }

    protected function details(\Psr\Http\Message\ServerRequestInterface $request, array<string,mixed> $details): array<string,mixed>
    {
        return [];
    }
}
