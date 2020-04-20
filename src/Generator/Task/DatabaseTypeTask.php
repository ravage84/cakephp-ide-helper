<?php

namespace IdeHelper\Generator\Task;

use Cake\Database\Type;
use Exception;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\ValueObject\ClassName;
use IdeHelper\ValueObject\StringName;

class DatabaseTypeTask implements TaskInterface {

	public const CLASS_TYPE = Type::class;

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$result = [];

		$types = $this->getTypes();

		$map = [];
		foreach ($types as $type => $className) {
			$map[$type] = ClassName::create($className);
		}
		ksort($map);

		$method = '\\' . static::CLASS_TYPE . '::build(0)';
		$directive = new Override($method, $map);
		$result[$directive->key()] = $directive;

		$list = [];
		foreach ($types as $type => $className) {
			$list[$type] = StringName::create($type);
		}
		ksort($list);

		$method = '\\' . static::CLASS_TYPE . '::map()';
		$directive = new ExpectedArguments($method, 0, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function getTypes(): array {
		$types = [];

		try {
			$allTypes = Type::buildAll();
		} catch (Exception $exception) {
			return $types;
		}

		foreach ($allTypes as $key => $type) {
			$types[$key] = get_class($type);
		}

		ksort($types);

		return $types;
	}

}
