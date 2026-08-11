<?php

namespace PressbooksNetworkCatalog\Validators;

use Illuminate\Http\Request;
use PressbooksNetworkCatalog\Contracts\Validator;

class DateValidator implements Validator
{
    private array $values;

    public function validate($data): bool
    {
        return \is_string($data) && \strtotime($data) !== false && $this->extraRules($data);
    }

    private function extraRules($data): bool
    {
        $request = Request::capture();
        $compareTo = $this->values['greaterThanOrEqualTo'] ?? null;

        if ($compareTo && $request->get($compareTo)) {
            $compareVal = $request->get($compareTo);
            $compareTs = \strtotime($compareVal);
            $dataTs = \strtotime($data);

            // If either value does not parse as a date, treat as invalid.
            if ($compareTs === false || $dataTs === false) {
                return false;
            }

            return $compareTs <= $dataTs;
        }

        return true;
    }

    private function setValues(array $data): void
    {
        $this->values = $data;
    }

    public function rules(array $data): Validator
    {
        $this->setValues($data);

        return $this;
    }
}
