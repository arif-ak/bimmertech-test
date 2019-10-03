<?php

namespace AppBundle\Helper;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * Class PaginateHelper
 * @package AppBundle\Helper
 */
class PaginateHelper
{

    /**
     * Paginate array
     *
     * @param array $array
     * @return array
     */
    public function paginateArray(array $array): array
    {
        $page = (int)(isset($_GET['page']) ? $_GET['page'] : 1) - 1;
        $page = $page === -1 ? 0 : $page;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $start = $page === 0 ? 0 : $page * $limit;

        $data = $this->sort($array);

        return [
            'page' => $page + 1,
            'limit' => $limit,
            'pages' => ceil(count($array) / $limit),
            'next' => $page + 1 < ceil(count($array) / $limit) ? $page + 2 : null,
            'previous' => $page > 0 ? $page : null,
            'data' => array_slice($data, $start, $limit),
        ];
    }

    /**
     * Sort array
     *
     * @param $data
     * @return array
     */
    private function sort($data)
    {
        $collection = new ArrayCollection($data);
        $iterator = $collection->getIterator();
        $sort = (isset($_GET['sort']) ? $_GET['sort'] : 'id');
        $order = (isset($_GET['order']) ? $_GET['order'] : 'ASC');

        $iterator->uasort(function ($a, $b) use ($sort, $order) {

            if (isset($a[$sort]) && isset($b[$sort])) {
                    if ($order == 'ASC' && $sort=='createdAt') {
                        return (new \DateTime($a[$sort]) > new \DateTime($b[$sort])) ? -1 : 1;
                    }
                    if ($order == 'DESC'&& $sort=='createdAt') {
                        return (new \DateTime($a[$sort]) < new \DateTime($b[$sort])) ? -1 : 1;
                    }

                    if ($order == 'ASC') {
                        return ($a[$sort] < $b[$sort]) ? -1 : 1;
                    }
                    if ($order == 'DESC') {
                        return ($a[$sort] > $b[$sort]) ? -1 : 1;
                    }
            }
        });

        $amenitiesSorted = new ArrayCollection(iterator_to_array($iterator));

        return $amenitiesSorted->toArray();
    }

}