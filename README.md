# StrictJson

A JSON mapping library that does one thing, and does it okay.

Create your own simple, unannotated, php classes and have StrictJson turn JSON into your models. If the JSON doesn't
have all the required properties or if the values don't match the types in your constructor, StrictJson will throw an
exception, so if it returns successfully, you know your model is completely instantiated with the correct types.

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
<?php
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
    private $age;
    private $address;

    public function __construct(string $name, int $age, Address $address) {
        $this->name = $name;
        $this->age = $age;
        $this->address = $address;
    }
    
    /** Getters omitted for brevity */
}
```

This code:
```php
<?php
$mapper = new StrictJson();
$user = $mapper->map($json, User::class);
var_dump($user);
```

Results in the following output
```
class \User#17 (3) {
  private $name =>
  string(8) "Joe User"
  private $age =>
  int(4)
  private $address =>
  class \Address#18 (2) {
    private $street =>
    string(13) "1234 Fake St."
    private $zip_code =>
    string(5) "12345"
  }
```

## Custom Class Mapping

Create a custom class adapter like this:
```php
<?php
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
<?php
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

// Register your adapter
$mapper = new StrictJson([DateTime::class => new DateAdapter()]);
$event = $mapper->map($json, DateTime::class);

echo $event->getDate()->format("y");
// Prints "2013"
```

## More Advanced Configuration
For limited configuration, the constructor style instantiation is fine, but for more advanced configuration, a builder
is provided as a fluent api for constructing your StrictJson instance. It can be used like this

```php
<?php
StrictJson::builder()
    ->addClassAdapter(Foo::class, new FooAdapter())
    ->addClassAdapter(Bar::class, new BarAdapter())
    ->build();
```

## Custom Parameter Mapping

If you only want to map a single parameter of a class, you can use parameter adapters:

```php
<?php
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Fixtures\Example\DateAdapter;
use Burba\StrictJson\Fixtures\Example\Event;

// Create your adapter as normal
class LenientBooleanAdapter
{
    public function fromJson(StrictJson $delegate, $parsed_value): bool
    {
        return (bool)$parsed_value;
    }
}

$json = '
{
    "name": "Dinner party for Bob",
    "date": "2013-02-13T08:35:34Z",
    "is_suit_required": 1
}
';

$mapper = StrictJson::builder()
    ->addClassAdapter(DateTime::class, new DateAdapter())
    // Register it as a parameter adapter
    ->addParameterAdapter(Event::class, 'is_suit_required', new LenientBooleanAdapter())
    ->build();

/** @var Event $event */
$event = $mapper->map($json, Event::class);
echo $event->isSuitRequired() ? 'Suit up' : 'Wear something casual';
// Prints "Suit up"
```

## Array item types

If your class contains arrays, you'll need to tell StrictJson the expected array item type, so that it can instantiate
those for you as well

```php
<?php
use Burba\StrictJson\Fixtures\Example\Address;
use Burba\StrictJson\Fixtures\Example\Event;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Fixtures\Example\DateAdapter;

// User class with array of events
class User
{
    /** @var string */
    private $name;
    /** @var int */
    private $age;
    /** @var Address */
    private $address;
    /** @var Event[] */
    private $events_attended;

    public function __construct(string $name, int $age, Address $address, array $events_attended = [])
    {
        $this->name = $name;
        $this->age = $age;
        $this->address = $address;
        $this->events_attended = $events_attended;
    }
}

$json = '
{
    "name": "Tim Fabulous",
    "age": 40,
    "address": {
        "street": "1234 Fake St.",
        "zip_code": "12345"
    },
    "events_attended": [
        {
            "name": "Dinner party for Bob",
            "date": "2013-02-13T08:35:34Z"
        }
    ]
}
';

$mapper = StrictJson::builder()
    ->addClassAdapter(DateTime::class, new DateAdapter())
    // Tell the mapper the events_attended parameter in the User class is an array of Events
    ->addParameterArrayAdapter(User::class, 'events_attended', Event::class)
    ->build();
```

## TODO

* Add docs for optional parameters, nullable fields (See StrictJsonTest::testBasicClass for now)
* Better error messages
* Always include complete json in thrown exceptions
