<?php declare(strict_types=1);

namespace Burba\StrictJson;

/**
 * Adapter that converts decoded portion of the mapped json to the type it is registered for
 */
interface Adapter
{
    /**
     * Convert decoded json into the specified type
     *
     * @param array|int|string|float|bool $decoded_json The decoded JSON value to adapt. StrictJson will verify that it
     * is one of the types returned from your fromTypes() method before passing it here. This will just be the part of
     * the decoded JSON that the adapter was registered for, not the entire JSON
     *
     * @param StrictJson $delegate You can use this instance of StrictJson to delegate mapping decoded json to
     * StrictJson.
     * @param JsonContext $context This gives you the JSON path of the position of the decoded json passed to this
     * method. It's meant to be used to provide more detailed error messages either by passing to JsonFormatException or
     * by passing it to StrictJson when delegating
     *
     * @return mixed The mapped JSON. Though the interface does not specify a return type, implementations of this
     * interface can and should do so.
     * @throws JsonFormatException If the decoded_json is not in the required format
     *
     * @see ArrayAdapter For an example implementation that uses all three parameters
     */
    public function fromJson($decoded_json, StrictJson $delegate, JsonContext $context);

    /**
     * The list of types this adapter supports in the decoded_json parameter of the fromJson method. StrictJson will
     * verify that the source JSON is one of these types before using this adapter.
     *
     * @return Type[]
     */
    public function fromTypes(): array;
}
