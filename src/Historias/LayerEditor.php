<?php
namespace Historias\Importer;

use Historias\Importer\Util\JsonUtil;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;

class LayerEditor
{
    private $file;

    private $importer;

    private $validator;

    private $schema;

    public function __construct(string $file, Importer $importer, Validator $validator, string $schema)
    {
        $this->file = $file;
        $this->importer = $importer;
        $this->validator = $validator;
        $this->schema = $schema;
    }

    public function edit(Filter $filter, ProgressLogger $progressLogger, ErrorLogger $errorLogger)
    {
        $data = $this->importer->import($filter, $progressLogger, $errorLogger);
        usort(
            $data,
            function (array $left, array $right) {
                if (isset($left['two_letter_iso_code'], $right['two_letter_iso_code'])) {
                    $result = strnatcasecmp($left['two_letter_iso_code'], $right['two_letter_iso_code']);
                    if ($result !== 0) {
                        return $result;
                    }
                }
                return strnatcasecmp($left['name'], $right['name']);
            }
        );

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve($this->schema);

        $this->validator->check(json_decode(json_encode($data)), $schema);

        if (!$this->validator->isValid()) {
            printf('Error validating JSON');
            foreach ($this->validator->getErrors() as $error) {
                echo sprintf("[%s] %s\n", $error['property'], $error['message']);
            }

            return;
        }
        file_put_contents($this->file, JsonUtil::encode($data));
    }
}
