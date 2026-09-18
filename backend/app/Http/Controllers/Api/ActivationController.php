<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activation;
use App\Models\ActivationMedia;
use App\Models\ActivationFaq;
use App\Models\ActivationTestimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    /**
     * Display a listing of activations
     */
    public function index(Request $request)
    {
        $query = Activation::with(['media', 'faqs', 'testimonials'])
            ->withCount(['events', 'destinations']);

        // Filter by active status (default: only active)
        if ($request->has('include_inactive')) {
            // Include all
        } else {
            $query->active();
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->featured();
        }

        // Order by sort_order and name
        $query->orderBy('sort_order')->orderBy('name');

        $activations = $query->get();

        return response()->json($activations);
    }

    /**
     * Display the specified activation by slug
     */
    public function show($slug)
    {
        $activation = Activation::where('slug', $slug)
            ->with([
                'media' => function($query) {
                    $query->orderBy('sort_order');
                },
                'faqs' => function($query) {
                    $query->orderBy('sort_order');
                },
                'testimonials' => function($query) {
                    $query->orderBy('sort_order');
                },
                'events' => function($query) {
                    $query->orderBy('date', 'desc');
                },
                'destinations' => function($query) {
                    $query->with('category')->orderBy('likes_count', 'desc');
                }
            ])
            ->firstOrFail();

        // Add computed properties for events
        $activation->upcoming_events = $activation->events()
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        $activation->past_events = $activation->events()
            ->where('date', '<', now())
            ->orderBy('date', 'desc')
            ->get();

        $activation->next_event = $activation->events()
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->first();
        
        // Add featured event (most recent upcoming event with poster, fallback to first upcoming)
        $activation->featured_event = $activation->events()
            ->where('date', '>=', now())
            ->whereNotNull('poster')
            ->orderBy('date', 'asc')
            ->first();
        
        // Fallback to first upcoming event if no event has poster
        if (!$activation->featured_event) {
            $activation->featured_event = $activation->events()
                ->where('date', '>=', now())
                ->orderBy('date', 'asc')
                ->first();
        }
        
        // Add popular destinations (top 4 by likes)
        $activation->popular_destinations = $activation->destinations()
            ->with('category')
            ->orderBy('likes_count', 'desc')
            ->limit(4)
            ->get();

        return response()->json($activation);
    }

    /**
     * Store a newly created activation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:activations,slug',
            'category' => 'required|in:city,theme,space,other',
            'city' => 'nullable|string|max:100',
            'city_latitude' => 'nullable|numeric',
            'city_longitude' => 'nullable|numeric',
            'short_title' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|max:2048',
            'icon' => 'nullable|image|max:1024',
            'color_theme' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'cta_primary_label' => 'nullable|string|max:100',
            'cta_primary_url' => 'nullable|url|max:255',
            'cta_secondary_label' => 'nullable|string|max:100',
            'cta_secondary_url' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'social_instagram' => 'nullable|string|max:100',
            'social_twitter' => 'nullable|string|max:100',
            'social_linktree' => 'nullable|url|max:255',
            'show_gallery' => 'boolean',
            'show_testimonials' => 'boolean',
            'show_faq' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $request->file('hero_image')->store('activations/heroes', 'public');
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('activations/icons', 'public');
        }

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $activation = Activation::create($validated);

        return response()->json([
            'message' => 'Activation created successfully',
            'activation' => $activation
        ], 201);
    }

    /**
     * Update the specified activation
     */
    public function update(Request $request, $id)
    {
        $activation = Activation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|unique:activations,slug,' . $id,
            'category' => 'sometimes|required|in:city,theme,space,other',
            'city' => 'nullable|string|max:100',
            'city_latitude' => 'nullable|numeric',
            'city_longitude' => 'nullable|numeric',
            'short_title' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|max:2048',
            'icon' => 'nullable|image|max:1024',
            'color_theme' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'cta_primary_label' => 'nullable|string|max:100',
            'cta_primary_url' => 'nullable|url|max:255',
            'cta_secondary_label' => 'nullable|string|max:100',
            'cta_secondary_url' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'social_instagram' => 'nullable|string|max:100',
            'social_twitter' => 'nullable|string|max:100',
            'social_linktree' => 'nullable|url|max:255',
            'show_gallery' => 'boolean',
            'show_testimonials' => 'boolean',
            'show_faq' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            // Delete old image
            if ($activation->hero_image) {
                Storage::disk('public')->delete($activation->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('activations/heroes', 'public');
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($activation->icon) {
                Storage::disk('public')->delete($activation->icon);
            }
            $validated['icon'] = $request->file('icon')->store('activations/icons', 'public');
        }

        $activation->update($validated);

        return response()->json([
            'message' => 'Activation updated successfully',
            'activation' => $activation
        ]);
    }

    /**
     * Remove the specified activation
     */
    public function destroy($id)
    {
        $activation = Activation::findOrFail($id);

        // Delete associated files
        if ($activation->hero_image) {
            Storage::disk('public')->delete($activation->hero_image);
        }
        if ($activation->icon) {
            Storage::disk('public')->delete($activation->icon);
        }

        // Delete media files
        foreach ($activation->media as $media) {
            if ($media->file_url) {
                Storage::disk('public')->delete($media->file_url);
            }
        }

        // Delete testimonial photos
        foreach ($activation->testimonials as $testimonial) {
            if ($testimonial->author_photo) {
                Storage::disk('public')->delete($testimonial->author_photo);
            }
        }

        $activation->delete();

        return response()->json([
            'message' => 'Activation deleted successfully'
        ]);
    }

    /**
     * Upload media for activation
     */
    public function uploadMedia(Request $request, $id)
    {
        $activation = Activation::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:photo,video,youtube',
            'file' => 'required_if:type,photo,video|file|max:10240',
            'external_url' => 'required_if:type,youtube|url',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $mediaData = [
            'activation_id' => $activation->id,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('file')) {
            $mediaData['file_url'] = $request->file('file')->store('activations/media', 'public');
        }

        if (isset($validated['external_url'])) {
            $mediaData['external_url'] = $validated['external_url'];
        }

        $media = ActivationMedia::create($mediaData);

        return response()->json([
            'message' => 'Media uploaded successfully',
            'media' => $media
        ], 201);
    }

    /**
     * Delete media
     */
    public function deleteMedia($id, $mediaId)
    {
        $activation = Activation::findOrFail($id);
        $media = ActivationMedia::where('activation_id', $activation->id)
            ->where('id', $mediaId)
            ->firstOrFail();

        if ($media->file_url) {
            Storage::disk('public')->delete($media->file_url);
        }

        $media->delete();

        return response()->json([
            'message' => 'Media deleted successfully'
        ]);
    }

    /**
     * Add FAQ
     */
    public function storeFaq(Request $request, $id)
    {
        $activation = Activation::findOrFail($id);

        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $faq = ActivationFaq::create([
            'activation_id' => $activation->id,
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'FAQ added successfully',
            'faq' => $faq
        ], 201);
    }

    /**
     * Update FAQ
     */
    public function updateFaq(Request $request, $id, $faqId)
    {
        $activation = Activation::findOrFail($id);
        $faq = ActivationFaq::where('activation_id', $activation->id)
            ->where('id', $faqId)
            ->firstOrFail();

        $validated = $request->validate([
            'question' => 'sometimes|required|string',
            'answer' => 'sometimes|required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $faq->update($validated);

        return response()->json([
            'message' => 'FAQ updated successfully',
            'faq' => $faq
        ]);
    }

    /**
     * Delete FAQ
     */
    public function deleteFaq($id, $faqId)
    {
        $activation = Activation::findOrFail($id);
        $faq = ActivationFaq::where('activation_id', $activation->id)
            ->where('id', $faqId)
            ->firstOrFail();

        $faq->delete();

        return response()->json([
            'message' => 'FAQ deleted successfully'
        ]);
    }

    /**
     * Add Testimonial
     */
    public function storeTestimonial(Request $request, $id)
    {
        $activation = Activation::findOrFail($id);

        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_photo' => 'nullable|image|max:1024',
            'quote' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $testimonialData = [
            'activation_id' => $activation->id,
            'author_name' => $validated['author_name'],
            'quote' => $validated['quote'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('author_photo')) {
            $testimonialData['author_photo'] = $request->file('author_photo')->store('activations/testimonials', 'public');
        }

        $testimonial = ActivationTestimonial::create($testimonialData);

        return response()->json([
            'message' => 'Testimonial added successfully',
            'testimonial' => $testimonial
        ], 201);
    }

    /**
     * Update Testimonial
     */
    public function updateTestimonial(Request $request, $id, $testimonialId)
    {
        $activation = Activation::findOrFail($id);
        $testimonial = ActivationTestimonial::where('activation_id', $activation->id)
            ->where('id', $testimonialId)
            ->firstOrFail();

        $validated = $request->validate([
            'author_name' => 'sometimes|required|string|max:100',
            'author_photo' => 'nullable|image|max:1024',
            'quote' => 'sometimes|required|string',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('author_photo')) {
            // Delete old photo
            if ($testimonial->author_photo) {
                Storage::disk('public')->delete($testimonial->author_photo);
            }
            $validated['author_photo'] = $request->file('author_photo')->store('activations/testimonials', 'public');
        }

        $testimonial->update($validated);

        return response()->json([
            'message' => 'Testimonial updated successfully',
            'testimonial' => $testimonial
        ]);
    }

    /**
     * Delete Testimonial
     */
    public function deleteTestimonial($id, $testimonialId)
    {
        $activation = Activation::findOrFail($id);
        $testimonial = ActivationTestimonial::where('activation_id', $activation->id)
            ->where('id', $testimonialId)
            ->firstOrFail();

        if ($testimonial->author_photo) {
            Storage::disk('public')->delete($testimonial->author_photo);
        }

        $testimonial->delete();

        return response()->json([
            'message' => 'Testimonial deleted successfully'
        ]);
    }
}
