<?php

declare(strict_types=1);

namespace NaassonTeam\LogViewer\Contracts;

use NaassonTeam\LogViewer\Contracts\Utilities\Filesystem;

/**
 * Interface  Patternable
 *
 * @package   NaassonTeam\LogViewer\Contracts
 * @author    NaassonTeam <info@naasson.com>
 */
interface Patternable
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern();

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     *
     * @return self
     */
    public function setPattern(
        $prefix    = Filesystem::PATTERN_PREFIX,
        $date      = Filesystem::PATTERN_DATE,
        $extension = Filesystem::PATTERN_EXTENSION
    );
}
