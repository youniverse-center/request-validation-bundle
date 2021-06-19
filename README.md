# request-validation-bundle

1. Add validator class implementing `Yc\RequestValidationBundle\RequestValidator\RequestValidatorInterface`
* in the `getConstriant` return constraints used by the validator component
* in the `getGroups` return validation groups
* `getInvalidRequestResponse` must return response that will be used if the validation has failed.

2. Add attribute `Yc\RequestValidationBundle\Attributes\RequestValidator` to your controller

```php
#[Route('/some/route', name: 'some_route')]
#[RequestValidator(Create::class)]
class CreateController extends AbstractController
{
  public function __invoke($data)
  {
    // in the data is your validated request content
  }
}
```

(This attribute can be also placed on a method if you have multple controllers in a class.)

3. You probably want to receive data from the request for the validation in your specific way. For this purpose implement `Yc\RequestValidationBundle\DataReceiver\DataReceiverInterface`

for example:
```php
public function getData(Request $request): mixed
{
    return json_decode($request->getContent(), true);
}
```

4. By default the data will be set in the request attribute `data` if you want to change this, implement `Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface`

for example:
```php
public function transformData(mixed $data): array
{
    $id = ProjectId::fromString($data['id']);

    return [
        'project' => new Project($id, $data['name'])
    ];
}
```

and then you can use it in controller like:
```
public function __invoke(Project $project) {}
```
