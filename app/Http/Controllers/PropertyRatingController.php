<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyRating;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PropertyRatingController extends Controller
{
    /**
     * Rate a property (1-5 stars)
     */
    public function rateProperty(Request $request, int $propertyId): JsonResponse
    {
        try {
            // Validate the rating value
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5'
            ]);

            // Check if property exists
            $property = Property::findOrFail($propertyId);

            // Get authenticated user
            $user = Auth::user();

            // Create or update the rating
            $propertyRating = PropertyRating::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'property_id' => $propertyId,
                ],
                [
                    'rating' => $validated['rating']
                ]
            );

            // Calculate new average rating
            $averageRating = $property->ratings()->avg('rating');

            return response()->json([
                'user_rating' => $propertyRating->rating,
                'average_rating' => round($averageRating, 1),
                'message' => 'Avaliação registrada com sucesso'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'A avaliação deve ser um número entre 1 e 5'
            ], 400);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Propriedade não encontrada'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
