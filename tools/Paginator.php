<?php
namespace Tools;

/**
 * Simplifies the creation of pagination for pages.
 *
 * Usage:
 * $paginator = new Paginator($totalItems, $currentPage, $perPage);
 * $html = $paginator->render();
 */
class Paginator {

    private $totalItems;
    private $currentPage;
    private $perPage;
    private $neighbours;
    private $totalPages;
    private $offset;
    private $paramName;

    /**
     * Constructor.
     *
     * @param int $totalItems Total number of items in the database
     * @param int $currentPage The current page
     * @param int $perPage Items per page
     * @param int $neighbours Number of neighboring pages at the left and the right sides
     * @param string $paramName Name of the GET parameter used in the links
     */
    public function __construct($totalItems, $currentPage, $perPage, $neighbours = 4, $paramName = 'page') {
        $this->totalItems = (int) $totalItems;
        $this->currentPage = (int) $currentPage;
        $this->perPage = (int) $perPage;
        $this->neighbours = (int) $neighbours;

        if ($this->perPage <= 0)
            throw new Exception('$perPage must at least be 1');

        if ($this->neighbours <= 0)
            throw new Exception('$neighbours must at least be 1');

        if ($this->currentPage < 1)
            $this->currentPage = 1;

        $this->totalPages = (int) ceil($this->totalItems / $this->perPage);

        if ($this->totalPages === 0)
            $this->totalPages = 1;

        if ($this->currentPage > $this->totalPages)
            $this->currentPage = $this->totalPages;

        $this->offset = abs(intval($this->currentPage * $this->perPage - $this->perPage));

        $this->paramName = $paramName;
    }

    public function offset() {
        return $this->offset;
    }

    public function limit() {
        return $this->perPage;
    }

    private function isFirst() {
        return $this->currentPage === 1;
    }

    private function isLast() {
        return $this->currentPage === $this->totalPages;
    }

    /**
     * Returns an array that looks something like this:
     * [
     *    1 => false,
     *    2 => true,
     *    3 => false
     * ]
     * In the example above the second page is the active page.
     *
     * @return array
     */
    private function pages() {
        $pages = [];

        $start = $this->currentPage - $this->neighbours < 1
            ? 1 : $this->currentPage - $this->neighbours;

        $end = $this->currentPage + $this->neighbours > $this->totalPages
            ? $this->totalPages : $this->currentPage + $this->neighbours;

        for ($i = $start; $i <= $end; $i++) {
            $pages[$i] = $i === $this->currentPage;
        }

        return $pages;
    }

    public function render() {
        ob_start();

        echo '<nav>';
        echo '<ul class="pagination">';
        echo '<li>';
        echo '<a href="?'.$this->paramName.'=1" aria-label="First">';
        echo '<span aria-hidden="true">&laquo;</span>';
        echo '</a>';
        echo '</li>';
        if ($this->isFirst())
            echo '<li class="disabled">';
        else
            echo '<li>';
        if ($this->isFirst())
            echo '<a href="#" aria-label="Previous">';
        else
            echo '<a href="?'.$this->paramName.'='.($this->currentPage - 1).'" aria-label="Previous">';
        echo '<span aria-hidden="true">&lsaquo;</span>';
        echo '</a>';
        echo '</li>';

        foreach ($this->pages() as $page => $isActive) {
            if ($isActive) {
                echo '<li class="active"><a href="?'.$this->paramName.'='.$page.'">'.$page.'</a></li>';
            } else {
                echo '<li><a href="?'.$this->paramName.'='.$page.'">'.$page.'</a></li>';
            }
        }

        if ($this->isLast())
            echo '<li class="disabled">';
        else
            echo '<li>';
        if ($this->isLast())
            echo '<a href="#" aria-label="Next">';
        else
            echo '<a href="?'.$this->paramName.'='.($this->currentPage + 1).'" aria-label="Next">';
        echo '<span aria-hidden="true">&rsaquo;</span>';
        echo '</a>';
        echo '</li>';
        echo '<li>';
        echo '<a href="?'.$this->paramName.'='.($this->totalPages).'" aria-label="Last">';
        echo '<span aria-hidden="true">&raquo;</span>';
        echo '</a>';
        echo '</li>';
        echo '</ul>';
        echo '</nav>';

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}