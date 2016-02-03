<?php

/**
 * Lightweight fluent model
 * Implements ArrayObject
 *
 * @author  Jack D. Taylor <msec.nt@gmail.com>
 * @package LWModel
 * @example <?php
 * class Coolest_Model_Ever extends LWModel {}
 * $my_model = new Coolest_Model_Ever();
 * $my_model->setName('Bob')->setAge(19);
 * echo $my_model->getAge(); // 19
 * echo $my_model->getName(); // Bob
 * $my_model->unsetAge()->unsetName();
 * ?>
 * @license Licensed under WTFPL
 *
 *            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *                    Version 2, December 2004
 *
 * Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 *            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 *  0. You just DO WHAT THE FUCK YOU WANT TO.
 */

abstract class LWModel extends ArrayObject {
	/**
	 * When used in preg_replace with "_$0" as second argument, converts CamelCaseVariable into snake_case_variable
	 * @var string
	 */
	const PREG_CAMEL_CASE = '/(?<=[A-Z])(?=[A-Z][a-z])|(?<=[^A-Z])(?=[A-Z])|(?<=[A-Za-z])(?=[^A-Za-z])/';

	/**
	 * Contains all the data
	 * @var array
	 */
	public $_data = array();

	/**
	 * Basically, a default ArrayObject constructor.
	 * @return void
	 */
	public function __construct(array $arguments = array()) {
		parent::__construct($arguments);

		$this->_onAfterConstruct($arguments);
	}

	/**
	 * Called after __construct is done
	 * @return null
	 */
	protected function _onAfterConstruct(array $arguments = array()) {

	}

	/**
	 * Processes unknown function call (eg. getSomeVariable) if it starts with one of following:
	 * - get* returns value stored by key
	 * - set* sets value in model and returns $this
	 * - isset* returns true or false whether key is presented in model
	 * - unset* unsets key in model and returns $this
	 *
	 * @throws LWModel_Exception if unable to process method
	 * @return mixed Result of corresponding method
	 */
	public function __call($method, array $arguments = array()) {
		if(!preg_match('/^(get|set|isset|unset)([A-Za-z0-9]+)$/', $method, $data)) {
			throw new LWModel_Exception('Method '.get_called_class().'::'.$method.' not found');
		}

		$property = strtolower(preg_replace(self::PREG_CAMEL_CASE, '_$0', $data[2]));

		switch($data[1]) {
			case 'get': {
				return $this->get($property);
			} break;

			case 'set': {
				return $this->set($property, $arguments[0]);
			} break;

			case 'unset': {
				return $this->_unset($property);
			} break;

			case 'isset': {
				return $this->_isset($property);
			} break;

			default: {
			}
		}
		return $this;
	}

	/** Alias for offsetExists */
	public function _isset($code) {
		return $this->offsetExists($code);
	}

	/** Alias for offsetGet */
	public function get($code) {
		return $this->offsetGet($code);
	}

	/** Alias for offsetSet */
	public function set($code, $value) {
		return $this->offsetSet($code, $value);
	}

	/** Alias for offsetUnset */
	public function _unset($code) {
		return $this->offsetUnset($code);
	}

	/**
	 * Returns true or false whether key is presented in model
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->_data[ $offset ]);
	}

	/**
	 * Returns value stored by key
	 * @return mixed Value
	 */
	public function offsetGet($offset) {
		if($this->offsetExists($offset)) {
			return $this->_data[ $offset ];
		}

		return null;
	}

	/**
	 * Sets value by key
	 * @return LWModel $this instance
	 */
	public function offsetSet($offset, $value) {
		$this->_data[ $offset ] = $value;
		return $this;
	}

	/**
	 * Removes key from this model
	 * @return LWModel $this instance
	 */
	public function offsetUnset($offset) {
		if($this->offsetExists($offset)) {
			unset($this->_data[ $offset ]);
		}

		return $this;
	}

	/**
	 * Returns iterator
	 * @return LWModel_Iterator $this instance
	 */
	public function getIterator() {
		return new LWModel_Iterator($this->_data);
	}
}

class LWModel_Iterator extends ArrayIterator {}
class LWModel_Exception extends Exception {}
