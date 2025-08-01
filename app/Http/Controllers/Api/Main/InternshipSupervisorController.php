<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Http\Resources\InternshipSupervisorResource;
use App\Models\InternshipSupervisor;
use Illuminate\Http\Request;

class InternshipSupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = InternshipSupervisor::all();

            return new InternshipSupervisorResource('Data fetched successfully', $data, 200);
        } catch (\Exception $e) {
            return new InternshipSupervisorResource('Failed to fetch data', [], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        try {
            $supervisor = InternshipSupervisor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return new InternshipSupervisorResource('Supervisor created successfully', $supervisor, 201);
        } catch (\Exception $e) {
            return new InternshipSupervisorResource('Failed to create supervisor: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    /*******  1244d415-5410-4ac0-825f-f46c709202db  *******/
    public function show(string $id)
    {
        try {
            $supervisor = InternshipSupervisor::findOrFail($id);

            return new InternshipSupervisorResource('Supervisor fetched successfully', $supervisor, 200);
        } catch (\Exception $e) {
            return new InternshipSupervisorResource('Failed to fetch supervisor: ' . $e->getMessage(), [], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $supervisor = InternshipSupervisor::findOrFail($id);

            $supervisor->update($request->only(['name', 'email', 'phone', 'address']));

            return new InternshipSupervisorResource('Supervisor updated successfully', $supervisor, 200);
        } catch (\Throwable $th) {
            return new InternshipSupervisorResource('Failed to update supervisor: ' . $th->getMessage(), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
