<?php
/**
 * Error class for missing error fields.
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * KiTT_MissingFieldsError
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_MissingFieldsError extends KiTT_Error implements KiTT_ErrorInterface
{

    /**
     * @var array Array of missing fields.
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param array $fields array of baptized missing fields.
     */
    public function __construct(array $fields)
    {
        parent::__construct('error_title_2');
        $this->fields = $fields;
    }

    /**
     * Error json to pass to the javascript.
     *
     * @return string JSON object to pass to the javascript.
     */
    public function json()
    {
        return json_encode(
            array('fields' => $this->fields)
        );
    }
}
