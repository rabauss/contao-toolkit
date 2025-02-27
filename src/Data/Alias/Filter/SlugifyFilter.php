<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Contao\StringUtil;
use Patchwork\Utf8;

use function html_entity_decode;
use function preg_replace;
use function strtolower;
use function trim;

use const ENT_QUOTES;

/**
 * SlugifyFilter creates a slug value of the columns being represented.
 */
final class SlugifyFilter extends AbstractValueFilter
{
    /**
     * Preserve uppercase.
     *
     * @var bool
     */
    private $preserveUppercase;

    /**
     * Encoding charset.
     *
     * @var string
     */
    private $charset;

    /**
     * Construct.
     *
     * @param list<string> $columns           Columns being used for the value.
     * @param bool         $break             If true break after the filter if value is unique.
     * @param int          $combine           Combine flag.
     * @param bool|mixed   $preserveUppercase If true uppercase values are not transformed.
     * @param string       $charset           Encoding charset.
     */
    public function __construct(
        array $columns,
        $break = true,
        $combine = self::COMBINE_REPLACE,
        $preserveUppercase = false,
        $charset = 'utf-8'
    ) {
        parent::__construct($columns, $break, $combine);

        $this->preserveUppercase = (bool) $preserveUppercase;
        $this->charset           = $charset;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($model, $value, string $separator): string
    {
        $values = [];

        foreach ($this->columns as $column) {
            $values[] = $this->slugify((string) $model->$column, $separator);
        }

        return $this->combine($value, $values, $separator);
    }

    /**
     * Slugify a value.
     *
     * @param string $value     Given value.
     * @param string $separator Separator string.
     */
    private function slugify(string $value, string $separator): string
    {
        $arrSearch  = ['/[^a-zA-Z0-9 \.\&\/_-]+/', '/[ \.\&\/-]+/'];
        $arrReplace = ['', $separator];

        $value = html_entity_decode($value, ENT_QUOTES, $this->charset);
        $value = StringUtil::stripInsertTags($value);
        $value = Utf8::toAscii($value);
        $value = preg_replace($arrSearch, $arrReplace, $value);

        if (! $this->preserveUppercase) {
            $value = strtolower($value);
        }

        return trim($value, $separator);
    }
}
