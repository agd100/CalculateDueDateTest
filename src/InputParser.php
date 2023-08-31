<?php

class InputParser
{
	public function parseInputFile ($filename)
	{
		$data = json_decode (file_get_contents ($filename));

		return $data;
	}
}

