<?php

namespace JoolistGrid\Grid\Column;

abstract class ColumnContent {
    const FORMAT_TEXT   = 'text';
    const FORMAT_DATE   = 'date';
    const FORMAT_STATUS = 'status';
    const FORMAT_LINK   = 'link';

	abstract public function getValue($row);
}