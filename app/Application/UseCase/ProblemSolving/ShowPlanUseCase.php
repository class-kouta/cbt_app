<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Infrastructure\Database\Models\ProblemSolvingPlan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ShowPlanUseCase
{
    /**
     * @return array<string, mixed>
     */
    public function handle(ProblemSolvingPlan $plan): array
    {
        $plan->loadMissing('problemSolving');

        $memberId = (int) Auth::id();
        if ($plan->problemSolving === null || (int) $plan->problemSolving->member_id !== $memberId) {
            throw (new ModelNotFoundException)->setModel(ProblemSolvingPlan::class, $plan->id);
        }

        return [
            'id' => $plan->id,
            'problem_solving_id' => $plan->problem_solving_id,
            'problem_situation' => $plan->problemSolving->problem_situation ?? '',
            'plan_number' => $plan->plan_number,
            'action_plan' => $plan->action_plan,
            'reflection' => $plan->reflection,
            'improvement_level' => $plan->improvement_level,
            'created_at' => $plan->created_at->format(DATE_ATOM),
            'updated_at' => $plan->updated_at->format(DATE_ATOM),
        ];
    }
}
