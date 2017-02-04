<?php

namespace CoderStudios\Library;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class BaseLibrary {

	protected $namespace = __NAMESPACE__;

	protected $basename;

	protected function getKeyName($string) {
		return md5(snake_case(str_replace('\\','',$this->namespace) . $this->basename . '_' . $string));
	}

	public function paginate($a, $total, $take, $page, $request)
	{
		return new Paginator($a,$total,$take,$page, [
			'path'  => $request->url(),
			'query' => $request->query(),
        ]);

	}

}