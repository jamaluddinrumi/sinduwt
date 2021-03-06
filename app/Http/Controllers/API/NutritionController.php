<?php

namespace App\Http\Controllers\API;

use App\Events\NutritionAdded;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\NutritionResource;
use Illuminate\Support\Facades\Validator;

class NutritionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return NutritionResource::collection(\App\Models\Nutrition::all()->load('customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'id' => 'required',
            'customerId' => 'required',
            'calories' => 'required',
            'fat' => 'required',
            'carbs' => 'required',
            'protein' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);
        }

        event(new NutritionAdded);

        return new NutritionResource(\App\Models\Nutrition::create([
            'customer_id' => $request->customerId,
            'calories' => $request->calories,
            'fat' => $request->fat,
            'carbs' => $request->carbs,
            'protein' => $request->protein,
        ])->load('customer'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nutrition  $nutrition
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $nutrition = Nutrition::findOrFail($id)->with('nutrition');

        // return response()->json($nutrition);

        return new NutritionResource(\App\Models\Nutrition::findOrFail($id)->load('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nutrition  $nutrition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            // 'customerId' => 'required',
            // 'calories' => 'required',
            // 'fat' => 'required',
            // 'carbs' => 'required',
            // 'protein' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);
        }

        $nutrition = \App\Models\Nutrition::findOrFail($request->id)->load('customer');

        if ($nutrition) {

            $nutrition->id = $request->id;
            $nutrition->calories = $request->calories;
            $nutrition->fat = $request->fat;
            $nutrition->carbs = $request->carbs;
            $nutrition->protein = $request->protein;
            $nutrition->save();

            return new NutritionResource($nutrition);
        }

        return response()->json(false, 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Nutrition  $nutrition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $nutrition = \App\Models\Nutrition::findOrFail($id)->load('customer');

        if ($nutrition) {

            $deleted_nutrition_id = $nutrition->delete();

            if ($deleted_nutrition_id) {

                return response()->json($deleted_nutrition_id);
            }


            return response()->json(false, 404);
        }
    }
}
