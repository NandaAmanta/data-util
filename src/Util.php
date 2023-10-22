<?

namespace Amanta\DataUtil;

use Amanta\DataUtil\Algorithm\FilterAlgorithm;
use Amanta\DataUtil\Algorithm\OrderAlgorithm;
use Amanta\DataUtil\Supports\Collection as SupportsCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;

class Util
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * get the query
     */
    public function get()
    {
        return $this->query;
    }

    /**
     * Static Sorting the data 
     */
    public static function order($query, $column = 'created_at', $relation = null, $orderType = "DESC")
    {
        $query = OrderAlgorithm::orderAlgorithm($query, $column, $relation, $orderType);
        return new Util($query);
    }

    /**
     * Sorting the data 
     */
    public function orderBy($column = 'created_at', $relation = null, $orderType = "DESC")
    {
        $this->query = OrderAlgorithm::orderAlgorithm($this->query, $column, $relation, $orderType);
        return $this;
    }

    /**
     *  Static Searching the data
     * 
     *  @param mixed $query the data/colelction/query builder
     *  @param array $scopeSearch array of fields/columns that can be searched
     *  @param string $searchValue requested search value
     *  
     *  @return mixed $query 
     */
    public static function search($query, $scopeSearch = [], $searchValue = null)
    {
        $query = self::searchAlgorithm($query, $scopeSearch, $searchValue);
        return new Util($query);
    }

    /**
     *  Searching the data
     * 
     *  @param array $scopeSearch array of fields/columns that can be searched
     *  @param string $searchValue requested search value
     *  
     *  @return mixed $query 
     */
    public function searchBy($scopeSearch = [], $searchValue = null)
    {
        $this->query = self::searchAlgorithm($this->query, $scopeSearch, $searchValue);
        return $this;
    }

    /**
     *  Static Filtering the data
     * 
     *  @param mixed $query the data/colelction/query builder
     *  @param array $filters requested filter
     *  @param array $scopeFilter array of fields/columns that can be filtered.
     *  Scope Filter 
     *  example : 
     *  [
     *      [query => 'permission_id', column => 'permissions-_id'],
     *      [query => 'name', column => 'name'],
     *  ]
     *  
     *  
     *  @return mixed $query 
     */
    public static function filter($query, $scopeFilter = [], $filters = [])
    {
        $query = FilterAlgorithm::filterAlgorithm($query, $scopeFilter, $filters);
        return new Util($query);
    }

    /**
     *  Filtering the data
     * 
     *  @param mixed $query the data/colelction/query builder
     *  @param array $filters requested filter
     *  @param array $scopeFilter array of fields/columns that can be filtered.
     *  Scope Filter 
     *  example : 
     *  [
     *      [query => 'permission_id', column => 'permissions-_id'],
     *      [query => 'name', column => 'name'],
     *  ]
     *  
     *  
     *  @return mixed $query 
     */
    public function filterBy($scopeFilter = [], $filters = [])
    {
        $this->query = FilterAlgorithm::filterAlgorithm($this->query, $scopeFilter, $filters);
        return $this;
    }

    /**
     * Paginate the data
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        if ($this->query instanceof BaseCollection) {
            return (new SupportsCollection($this->query))->paginate((int)$perPage);
        }
        return $this->query->paginate($perPage);
    }

    /**
     * Get Base Collection
     */
    public function toBaseCollection()
    {
        if ($this->query instanceof BaseCollection) {
            return $this->query;
        }
        return collect($this->query->get());
    }
}
