<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    /**
     * Listar encuestas disponibles para el usuario
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $surveys = Survey::with('questions')
            ->get()
            ->map(function ($survey) use ($userId) {
                $response = SurveyResponse::where('survey_id', $survey->id)
                    ->where('respondent_user_id', $userId)
                    ->first();

                return [
                    'id' => $survey->id,
                    'title' => $survey->title,
                    'description' => 'Ayúdanos a mejorar compartiendo tu experiencia', // Descripción genérica
                    'questions' => $survey->questions->map(function ($q) {
                        return [
                            'id' => $q->id,
                            'question' => $q->question_text,
                            'type' => $q->question_type,
                            'options' => $this->getQuestionOptions($q->question_type),
                        ];
                    }),
                    'completed' => $response && $response->completed_at !== null,
                    'completed_at' => $response?->completed_at,
                ];
            });

        return response()->json([
            'message' => 'Encuestas obtenidas exitosamente',
            'data' => $surveys
        ]);
    }

    /**
     * Obtener opciones predefinidas según el tipo de pregunta
     */
    private function getQuestionOptions($questionType)
    {
        if ($questionType === 'multiple_choice') {
            return [
                'Definitivamente sí',
                'Probablemente sí',
                'No estoy seguro',
                'Probablemente no',
                'Definitivamente no'
            ];
        }
        return null;
    }

    /**
     * Obtener una encuesta específica con sus preguntas
     */
    public function show(Request $request, $id)
    {
        $survey = Survey::with('questions')->find($id);

        if (!$survey) {
            return response()->json([
                'message' => 'Encuesta no encontrada'
            ], 404);
        }

        $response = SurveyResponse::where('survey_id', $survey->id)
            ->where('respondent_user_id', $request->user()->id)
            ->first();

        return response()->json([
            'message' => 'Encuesta obtenida exitosamente',
            'data' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'description' => 'Ayúdanos a mejorar compartiendo tu experiencia',
                'questions' => $survey->questions->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'question' => $q->question_text,
                        'type' => $q->question_type,
                        'options' => $this->getQuestionOptions($q->question_type),
                    ];
                }),
                'completed' => $response && $response->completed_at !== null,
                'answers' => $response?->answers ?? [],
            ]
        ]);
    }

    public function getUserResponses($surveyId)
{
    $userId = auth()->id();
    
    $responses = SurveyResponse::where('survey_id', $surveyId)
        ->where('user_id', $userId)
        ->get()
        ->map(function($response) {
            return [
                'question_id' => $response->question_id,
                'answer' => $response->answer
            ];
        });
    
    return response()->json([
        'data' => [
            'answers' => $responses
        ]
    ]);
}

    /**
     * Enviar respuestas de una encuesta
     */
    public function submitResponse(Request $request, $id)
    {
        $survey = Survey::with('questions')->find($id);

        if (!$survey) {
            return response()->json([
                'message' => 'Encuesta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que todas las preguntas fueron respondidas
        $questionIds = $survey->questions->pluck('id')->toArray();
        $answeredQuestionIds = array_keys($request->answers);
        
        if (count(array_diff($questionIds, $answeredQuestionIds)) > 0) {
            return response()->json([
                'message' => 'Debes responder todas las preguntas'
            ], 422);
        }

        // Crear o actualizar la respuesta
        $response = SurveyResponse::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'respondent_user_id' => $request->user()->id,
            ],
            [
                'answers' => $request->answers,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Respuestas enviadas exitosamente',
            'data' => $response
        ], 200);
    }

    /**
     * ADMIN: Crear una nueva encuesta
     */
    public function store(Request $request)
    {
        // Verificar que el usuario es administrador
        $userRoles = $request->user()->role ?? [];
        if (!in_array('admin', $userRoles) && !in_array('administrador', $userRoles)) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'target_type' => 'nullable|string|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:text,rating,multiple_choice',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $survey = Survey::create([
                'title' => $request->title,
                'target_type' => $request->target_type,
                'created_by_user_id' => $request->user()->id,
            ]);

            foreach ($request->questions as $questionData) {
                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Encuesta creada exitosamente',
                'data' => $survey->load('questions')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la encuesta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ADMIN: Actualizar una encuesta
     */
    public function update(Request $request, $id)
    {
        $userRoles = $request->user()->role ?? [];
        if (!in_array('admin', $userRoles) && !in_array('administrador', $userRoles)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $survey = Survey::find($id);
        if (!$survey) {
            return response()->json(['message' => 'Encuesta no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'target_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $survey->update($validator->validated());

        return response()->json([
            'message' => 'Encuesta actualizada exitosamente',
            'data' => $survey
        ]);
    }

    /**
     * ADMIN: Eliminar una encuesta
     */
    public function destroy(Request $request, $id)
    {
        $userRoles = $request->user()->role ?? [];
        if (!in_array('admin', $userRoles) && !in_array('administrador', $userRoles)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $survey = Survey::find($id);
        if (!$survey) {
            return response()->json(['message' => 'Encuesta no encontrada'], 404);
        }

        $survey->delete();

        return response()->json([
            'message' => 'Encuesta eliminada exitosamente'
        ]);
    }
}