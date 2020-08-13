<?php

declare(strict_types=1);

namespace NaassonTeam\LogViewer\Contracts;

/**
 * Interface  Table
 *
 * @package   NaassonTeam\LogViewer\Contracts
 * @author    NaassonTeam <info@naasson.com>
 */
interface Table
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get table header.
     *
     * @return array
     */
    public function header();

    /**
     * Get table rows.
     *
     * @return array
     */
    public function rows();

    /**
     * Get table footer.
     *
     * @return array
     */
    public function footer();
}
