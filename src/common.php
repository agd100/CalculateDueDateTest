<?php


class NotImplementedException extends BadMethodCallException
{}



define ('WORKING_HOURS', [9, 0, 17, 0]);
define ('WORK_OPEN_HOUR', 0); # array indexes
define ('WORK_OPEN_MINUTE', 1); # array indexes
define ('WORK_CLOSE_HOUR', 2); # array indexes
define ('WORK_CLOSE_MINUTE', 3); # array indexes

# %W   week number of year, with Monday as first day of week (00..53)
define ('WORKING_SATURDAYS', [2023 => [34, 35, 36]]);

