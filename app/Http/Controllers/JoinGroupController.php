<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinGroupRequest;
use App\Services\JoinGroupService;
use Illuminate\Http\Request;

class JoinGroupController extends Controller
{

    protected $JoinGroupService;

    public function __construct(JoinGroupService $joinGroupService)
    {
        $this->JoinGroupService = $joinGroupService;
    }
    public function JoinRequest($group_id){
        $done =$this->JoinGroupService->JoinGroup($group_id);
        if($done) return response()->json(["message"=>"request sent successfully"]);
        return response()->json(["message"=>"something went wrong"]);
    }

    public function AcceptJoin(JoinGroupRequest $request){
        $request->validated();
        $data= $this->JoinGroupService->AcceptJoin($request->join_id,$request->group_id);
        if($data)  return response()->json(["message"=>"accepted successfully"]);
        return response()->json(["message"=>"something went wrong"]);
    }

    public function RejectJoin(JoinGroupRequest $request){
        $data= $this->JoinGroupService->RejectJoin($request->join_id,$request->group_id);
        if($data)  return response()->json(["message"=>"rejected successfully"]);
        return response()->json(["message"=>"something went wrong"]);
    }

    public function getAllRequests($group_id){
        $all=$this->JoinGroupService->GetAllRequests($group_id);
        if($all===false) return response()->json(["message"=>"something went wrong"]);
        return response()->json(["data"=>$all]);
    }

}
