# StrictJson

A JSON parsing library that does one thing, and does it okay.

Create your own simple, unannotated, php classes and have StrictJson turn JSON into your models. If the JSON doesn't
have all the required properties, StrictJson will throw an exception, so if it returns successfully, you know your model
is completely instantiated.

Requires PHP 7.2+

## Usage

Given this json:
```json
{
  "name": "Joe User",
  "age": 4,
  "address": {
    "street": "1234 Fake St.",
    "zip_code": "12345"
  }
}
```
And these classes:
```php
class Address {
    private $street;
    private $zip_code;

    public function __construct(string $street, string $zip_code) {
        $this->street = $street;
        $this->zip_code = $zip_code;
    }
    
    /** Getters omitted for brevity */
}

class User {
    private $name;
    private $zip_code;

    public function __construct(string name, int age, Address $address) {
        $this->name = $name;
        $this->age = $age;
    }
    
    /** Getters omitted for brevity */
}
```

This code:
```php
$mapper = new StrictJson();
$user = $mapper->map($json, User::class);
var_dump($user);
```

Results in the following output
```
class Burba\StrictJson\Fixtures\Example\User#17 (3) {
  private $name =>
  string(8) "Joe User"
  private $age =>
  int(4)
  private $address =>
  class Burba\StrictJson\Fixtures\Example\Address#18 (2) {
    private $street =>
    string(13) "1234 Fake St."
    private $zip_code =>
    string(5) "12345"
  }
```

## Custom Mapping

Create a custom class adapter like this:
```php
class DateAdapter
{
    public function fromJson(StrictJson $delegate, string $parsed_json): DateTime
    {
        return DateTime::createFromFormat(DateTime::ISO8601, $parsed_json);
    }
}
```

And use it like this:
```php
class Event
{
    /** @var string */
    private $name;
    /** @var DateTime */
    private $date;

    public function __construct(string $name, DateTime $date)
    {
        $this->name = $name;
        $this->date = $date;
    }

    /** Getters omitted for brevity */
}

$json = '
{
    "name": "Dinner party for Bob",
    "date": "2013-02-13T08:35:34Z"
}
';

$mapper = StrictJson::builder()->addClassAdapter(DateTime::class, new DateAdapter())->build();
$event = $mapper->map($json, DateTime::class);

// Prints "2013"
echo $event->getDate()->format("y");
```

## TODO

* Write the rest of the tests
* Add docs for handling arrays (See StrictJsonTest::testIntArrayProperty for now)
* Add docs for optional parameters, nullable fields (See StrictJsonTest::testBasicClass for now)
* Better error messages
* Always include complete json in thrown exceptions
