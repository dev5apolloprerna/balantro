<?php 
use App\Http\Concerns\ResponseConcern;

class MyController extends Controller
{
    use ResponseConcern;
    
    public function myAction()
    {
        try {
            // Your logic here
            return $this->success("Operation successful", $data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), self::HTTP_STATUS_CODE_500, $e->getTrace());
        }
    }
}