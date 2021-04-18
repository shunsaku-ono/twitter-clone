<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
         $data = [];
        if (\Auth::check()) {
            // 認証済みユーザを取得
            $user = \Auth::user();
            
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            return view('tasks.index', [
                'tasks' => $tasks,
                ]);
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
    
            
            
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tasks = new Task;
        
        return view('tasks.create', [
           'tasks' => $tasks,
           ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required',
            'content' => 'required|max:255',
            ]);
            
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status'  => $request->status,
            ]);
       
        
      
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tasks = Task::findOrFail($id);
        
        return view('tasks.show', [
            'tasks' => $tasks,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tasks = Task::findOrFail($id);
        
        return view('tasks.edit', [
            'tasks' =>$tasks,
            ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'content' => 'required|max:255',
            ]);
            
        $tasks = Task::findOrFail($id);
        $tasks->status = $request->status;
        $tasks->content = $request->content;
        $tasks->save();
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tasks = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $tasks->user_id) {
            $tasks->delete();
        }
        
        return redirect('/');
    }
}
