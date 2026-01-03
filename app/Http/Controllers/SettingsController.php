<?php

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Models\MetricCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Show metrics management page.
     */
    public function metrics()
    {
        $categoriesWithMetrics = MetricCategory::ordered()
            ->with(['metrics' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->get();

        $categories = MetricCategory::active()->ordered()->get();

        return view('settings.metrics', [
            'categoriesWithMetrics' => $categoriesWithMetrics,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new metric.
     */
    public function storeMetric(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:metrics,key|max:50',
            'title' => 'required|string|max:255',
            'type' => 'required|in:boolean,scale',
            'min_value' => 'nullable|integer|required_if:type,scale|min:0',
            'max_value' => 'nullable|integer|required_if:type,scale|gt:min_value',
            'metric_category_id' => 'required|exists:metric_categories,id',
        ]);

        // Get the highest sort_order for new metrics
        $maxSort = Metric::max('sort_order') ?? 0;

        $metric = Metric::create([
            'key' => $validated['key'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'min_value' => $validated['min_value'] ?? null,
            'max_value' => $validated['max_value'] ?? null,
            'metric_category_id' => $validated['metric_category_id'],
            'is_active' => true,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('settings.metrics')
            ->with('status', "Метрика '{$metric->title}' добавлена успешно");
    }

    /**
     * Update a metric.
     */
    public function updateMetric(Request $request, Metric $metric)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'metric_category_id' => 'required|exists:metric_categories,id',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $metric->update([
            'title' => $validated['title'],
            'metric_category_id' => $validated['metric_category_id'],
            'is_active' => $request->boolean('is_active', false),
            'sort_order' => $validated['sort_order'] ?? $metric->sort_order,
        ]);

        return redirect()->route('settings.metrics')
            ->with('status', "Метрика '{$metric->title}' обновлена успешно");
    }

    /**
     * Show categories management page.
     */
    public function categories()
    {
        $categories = MetricCategory::ordered()->get();

        return view('settings.categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new user-defined category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:metric_categories,key|max:50',
            'title' => 'required|string|max:255',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $maxSort = MetricCategory::max('sort_order') ?? 0;

        $category = MetricCategory::create([
            'key' => $validated['key'],
            'title' => $validated['title'],
            'sort_order' => $validated['sort_order'] ?? $maxSort + 1,
            'is_active' => true,
            'is_user_defined' => true,
        ]);

        return redirect()->route('settings.categories')
            ->with('status', "Категория '{$category->title}' добавлена успешно");
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, MetricCategory $category)
    {
        $rules = [
            'is_active' => 'sometimes|boolean',
        ];

        // Only allow editing title and sort_order for user-defined categories
        if ($category->is_user_defined) {
            $rules['title'] = 'sometimes|string|max:255';
            $rules['sort_order'] = 'sometimes|integer|min:0';
        }

        $validated = $request->validate($rules);

        $updateData = [
            'is_active' => $request->boolean('is_active', $category->is_active),
        ];

        if ($category->is_user_defined) {
            if (isset($validated['title'])) {
                $updateData['title'] = $validated['title'];
            }
            if (isset($validated['sort_order'])) {
                $updateData['sort_order'] = $validated['sort_order'];
            }
        }

        $category->update($updateData);

        return redirect()->route('settings.categories')
            ->with('status', "Категория '{$category->title}' обновлена успешно");
    }

    /**
     * Delete a user-defined category.
     */
    public function deleteCategory(MetricCategory $category)
    {
        // Only allow deletion of user-defined categories
        if (!$category->is_user_defined) {
            return redirect()->route('settings.categories')
                ->with('error', 'Нельзя удалять системные категории');
        }

        // Check if category has metrics
        if ($category->metrics()->count() > 0) {
            return redirect()->route('settings.categories')
                ->with('error', "Нельзя удалять категорию '{$category->title}' - в ней есть метрики");
        }

        $title = $category->title;
        $category->delete();

        return redirect()->route('settings.categories')
            ->with('status', "Категория '{$title}' удалена успешно");
    }
}
