# What is StrictJson?

StrictJson turns JSON into instances of your plain old PHP classes

StrictJson examines the constructor of your model class and collects parameter names and types.
Then it validates the JSON to ensure that it has a property with a matching type for each required constructor
parameter. Finally, it instantiates your model classes (with their own constructor) and returns them to you.

For example, given the the JSON
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
<?php declare(strict_types=1);
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
<?php declare(strict_types=1);
$mapper = new StrictJson();
$user = $mapper->map($json, User::class);
var_dump($user);
```


Results in the following output
```
class \User#1 (3) {
  private $name =>
  string(8) "Joe User"
  private $age =>
  int(4)
  private $address =>
  class \Address#1 (2) {
    private $street =>
    string(13) "1234 Fake St."
    private $zip_code =>
    string(5) "12345"
  }
}
```

# Optional Fields
If your constructor parameter has a default value, StrictJson will use that value if the field does not exist in the
JSON.

Here's a minimal example:
```php
<?php declare(strict_types=1);
use Burba\StrictJson\StrictJson;

class ModelWithOptionalParam
{
    private $optional_param;

    public function __construct(string $optional_param = 'default')
    {
        $this->optional_param = $optional_param;
    }
    
    /** Getters omitted for brevity */
}

$mapper = new StrictJson();
$model = $mapper->map('{}', ModelWithOptionalParam::class);
echo $model->getOptionalParam();
// Prints 'default'
```

# Nullable Fields
If your constructor parameter has a nullable type, StrictJson will allow the JSON fields to be null as well.
Here's a minimal example:
```php
<?php declare(strict_types=1);
use Burba\StrictJson\StrictJson;

class ModelWithNullableParam
{
    private $nullable_param;

    public function __construct(?string $nullable_param)
    {
        $this->nullable_param = $nullable_param;
    }

    /** Getters omitted for brevity */
}

$json = '{"nullable_param": null}';

$mapper = new StrictJson();
$model = $mapper->map($json, ModelWithNullableParam::class);
$message = is_null($model->getNullableParam()) ? 'Param is null' : 'Param is not null';
echo $message;
// Prints 'Param is null'
```

# Custom Mapping

To customize how StrictJson turns JSON into your models, create a class that implements `Burba\StrictJson\Adapter` and
register it for a class or parameter when creating StrictJson. See below for examples of the different types of
adapters.

## Class Adapters

Sometimes your model classes have a parameter that does not have the same basic type as its JSON representation. In that
case, you can write a custom adapter to tell StrictJson how to parse that parameter.

For example, you can create a custom class adapter like this:
```php
<?php declare(strict_types=1);
use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonContext;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

class DateAdapter implements Adapter
{
    public function fromJson($decoded_json, StrictJson $delegate, JsonContext $context): DateTime
    {
        return DateTime::createFromFormat(DateTime::ISO8601, $decoded_json);
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [Type::string()];
    }
}
```

And use it like this:
```php
<?php declare(strict_types=1);
use Burba\StrictJson\Fixtures\Docs\DateAdapter;
use Burba\StrictJson\StrictJson;

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

## Parameter Adapters

If you only want to map a single parameter of a class, you can use a parameter adapter:

```php
<?php declare(strict_types=1);
use Burba\StrictJson\Adapter;
use Burba\StrictJson\Fixtures\Docs\Event;
use Burba\StrictJson\JsonContext;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;
use Burba\StrictJson\Fixtures\Docs\DateAdapter;

// Create your adapter as normal
class LenientBooleanAdapter implements Adapter
{
    public function fromJson($decoded_value, StrictJson $delegate, JsonContext $context): bool
    {
        return (bool)$decoded_value;
    }

    /** @return Type[] */
    public function fromTypes(): array
    {
        return [
            Type::int(),
            Type::bool(),
        ];
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

## Array Parameter Adapters

If your class contains arrays, you'll need to tell StrictJson the expected array item type, so that it can instantiate
those for you as well.

```php
<?php declare(strict_types=1);
use Burba\StrictJson\Fixtures\Docs\Address;
use Burba\StrictJson\Fixtures\Docs\Event;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Fixtures\Docs\DateAdapter;
use Burba\StrictJson\Type;


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
    
    /** Getters omitted for brevity */
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
    ->addParameterArrayAdapter(User::class, 'events_attended', Type::ofClass(Event::class))
    ->build();


$user = $mapper->map($json, User::class);
echo $user->getEventsAttended()[0]->getName();
// Prints "Dinner party for Bob"
```

# Custom Validation

If you want to validate more than just the parameter name and type of fields in the JSON, you can add a custom Adapter
that does the validation, or you can just validate in the constructor of your model class. Exceptions thrown in your
model constructor will be re-thrown by StrictJson when it attempts to map. Validation in your constructor has the
additional benefit of validating your models even when you're not using StrictJson.

# Exceptions

## JsonFormatException
If the JSON is invalid, is missing required fields specified by your model constructor, or has fields which don't match
the types of your model constructor, StrictJson will throw a `JsonFormatException`, to indicate that the JSON was not
formatted as you expected. `JsonFormatException` messages will also include a full path to the place in the JSON that
is causing the error, which looks like this (if expecting an `int` array in position `$.a.b`):

JSON:
```json
{
  "a": {
    "b": [1, "two", 3]
  }
}
```

Error:
```
Value is of type string, expected type int at path $.a.b[1]
```

## InvalidConfigurationException
If StrictJson is configured incorrectly, for example, by mapping to a class that doesn't have a constructor, it will
throw `InvalidConfigurationException`. StrictJson does not validate adapters until it actually uses them for
performance reasons, so InvalidConfigurationException may be thrown later than you expect.
