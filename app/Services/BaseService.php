<?php namespace App\Services;

use App\Exceptions\CustomException;
use App\Traits\Helper;
use Illuminate\Database\Eloquent\Model;

class BaseService
{

    use Helper;

    /**
     * Eloquent model.
     * @var Model
     */
    protected $model;

    /**
     * BaseService constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * All abstract eloquent method
     * @return mixed */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * WhereIn abstract eloquent method
     * @param $name
     * @param $arr
     * @return mixed
     */
    public function whereIn($name, $arr)
    {
        return $this->model->whereIn($name, $arr);
    }

    /**
     * WhereDate abstract eloquent method
     * @param $name
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function whereDate($name, $operator, $value)
    {
        return $this->model->whereDate($name, $operator, $value);
    }

    /**
     * WhereMonth abstract eloquent method
     * @param $name
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function whereMonth($name, $operator, $value)
    {
        return $this->model->whereDate($name, $operator, $value);
    }

    /**
     * Where abstract eloquent method
     * @param $name
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function where($name, $operator, $value)
    {
        return $this->model->where($name, $operator, $value);
    }

    /**
     * FindOrFail abstract method eloquent
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * FindOrFail abstract method eloquent
     * @param $id
     * @param string $message
     * @return mixed
     * @throws CustomException
     */
    public function findOrFail($id, $message = '')
    {

        if (!$exist = $this->model->find($id)) {
            if (!strlen($message)) $message = $this->classBasename(get_class($this->model)) . " id:$id not found";
            throw new CustomException($message, 404);
        }

        return $exist;
    }

}
