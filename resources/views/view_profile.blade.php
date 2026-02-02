@extends('layouts.app')

@section('title')
    {{ $profile->full_name }} | Uni-Serve
@endsection

@section('content')
    <div class="container" style="padding: 4rem 0;">
        <div class="card" style="max-width: 800px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                @if (!empty($profile->profile_picture))
                    <img src="{{ asset($profile->profile_picture) }}" alt="Profile Picture"
                        style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-color);">
                @else
                    <div
                        style="width: 150px; height: 150px; border-radius: 50%; background-color: #f0f0f0; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 4px solid #ddd;">
                        <span style="color: #888; font-size: 3rem;"><i class="fas fa-user"></i></span>
                    </div>
                @endif

                <h2 style="margin-top: 20px; color: var(--primary-color);">
                    {{ $profile->full_name }}
                    @if($profile->user->providerVerification?->status === 'approved')
                        <i class="fas fa-check-circle" style="color: var(--accent-blue); font-size: 0.8em;"
                            title="Verified Provider"></i>
                    @endif
                    @if(isset($averageRating) && $averageRating > 0)
                        <span style="font-size: 0.6em; color: #ffc107; margin-left: 10px;">
                            <i class="fas fa-star"></i> {{ number_format($averageRating, 1) }}
                        </span>
                    @endif
                </h2>
                <p style="color: #666; font-size: 1.1rem;">
                    {{ $profile->address ?? 'Unisan, Quezon' }}
                </p>
            </div>

            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">About</h3>
                    <p style="line-height: 1.6; color: #444;">
                        {!! nl2br(e($profile->bio ?? 'No bio available.')) !!}
                    </p>

                    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px;">
                        Contact Information</h3>
                    <p><strong><i class="fas fa-phone"></i> Phone:</strong>
                        {{ $profile->contact_number ?? 'Not provided' }}
                    </p>
                    <p><strong><i class="fas fa-map-marker-alt"></i> Address:</strong>
                        {{ $profile->address ?? 'Not provided' }}
                    </p>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Services Offered
                    </h3>
                    @if (count($skills) > 0)
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            @foreach ($skills as $skill)
                                <span style="background: #e9ecef; padding: 8px 15px; border-radius: 20px; color: #495057;">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p style="color: #888;">No services listed.</p>
                    @endif

                    <div style="margin-top: 40px; text-align: center;">
                        <button id="requestBtn" class="btn-primary"
                            style="display: inline-block; width: 100%; padding: 12px; margin-bottom: 10px; border:none; border-radius: 4px; cursor: pointer; font-size: 1rem;">Request
                            Service</button>
                        <a href="{{ route('search') }}" class="btn-outline-primary"
                            style="display: inline-block; width: 100%; text-align: center; padding: 12px; border-radius: 4px; text-decoration: none;">Back
                            to Search</a>
                    </div>
                </div>
            </div>

            <!-- Accomplishments Section -->
            <div style="margin-top: 40px; border-top: 2px solid #eee; padding-top: 30px;">
                <h3 style="color: var(--primary-color);">Accomplishments</h3>
                @if($accomplishments->count() > 0)
                    <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 25px;">
                        @foreach($accomplishments as $item)
                            <div style="flex: 0 0 auto; width: 150px; cursor: pointer;"
                                onclick="openLightbox('{{ asset($item->image_path) }}', '{{ $item->caption }}')">
                                <img src="{{ asset($item->image_path) }}" alt="Accomplishment"
                                    style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s;">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #888; font-style: italic;">No accomplishments to show.</p>
                @endif
            </div>

            <!-- Lightbox Modal -->
            <div id="lightboxModal"
                style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); justify-content: center; align-items: center;">
                <span onclick="document.getElementById('lightboxModal').style.display='none'"
                    style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
                <div style="text-align: center; max-width: 90%; max-height: 90%;">
                    <img id="lightboxImg"
                        style="max-width: 100%; max-height: 80vh; border-radius: 4px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                    <div id="lightboxCaption" style="color: #ccc; margin-top: 15px; font-size: 1.2rem;"></div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div style="margin-top: 40px; border-top: 2px solid #eee; padding-top: 30px;">
                <h3 style="color: var(--primary-color);">Reviews & Ratings</h3>
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <div style="font-size: 2.5rem; font-weight: bold; color: #333; margin-right: 15px;">
                        {{ number_format($averageRating ?? 0, 1) }}
                    </div>
                    <div>
                        <div style="color: #ffc107; font-size: 1.2rem;">
                            @php $rating = round($averageRating ?? 0); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div style="color: #666;">Based on {{ $reviewCount }} reviews</div>
                    </div>
                </div>

                @if (count($reviews) > 0)
                    <div style="margin-top: 20px;">
                        @foreach ($reviews as $review)
                            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <strong>{{ $review->seeker->profile->full_name ?? $review->seeker->username }}</strong>
                                    <span style="color: #888; font-size: 0.9em;">
                                        {{ $review->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <div style="color: #ffc107; margin-bottom: 8px; font-size: 0.9em;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p style="color: #555; margin: 0;">
                                    {{ $review->comment }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #888; font-style: italic;">No reviews yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Request Service Modal -->
    <div id="requestModal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div
            style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 500px; border-radius: 8px; position: relative;">
            <span id="closeModal"
                style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2 style="color: var(--primary-color);">Request Service</h2>
            <p>Send a request to <strong>{{ $profile->full_name }}</strong>.</p>

            <form action="{{ route('requests.store') }}" method="POST">
                @csrf
                <input type="hidden" name="provider_id" value="{{ $provider_id }}">

                <div class="form-group">
                    <label>Proposed Date & Time</label>
                    <input type="datetime-local" name="service_date" required
                        style="width: 100%; padding: 8px; margin-top: 5px;">
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Notes / Description of Issue</label>
                    <textarea name="notes" rows="4" placeholder="Describe what you need help with..."
                        style="width: 100%; padding: 8px; margin-top: 5px;"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px;">Send Request</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div
            style="position: fixed; bottom: 20px; right: 20px; background: #007bff; color: white; padding: 15px 25px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); animation: fadeOut 5s forwards;">
            {{ session('success') }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        const modal = document.getElementById("requestModal");
        const btn = document.getElementById("requestBtn");
        const span = document.getElementById("closeModal");

        if (btn) {
            btn.onclick = function () {
                modal.style.display = "block";
            }
        }

        if (span) {
            span.onclick = function () {
                modal.style.display = "none";
            }
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
            if (event.target == document.getElementById('lightboxModal')) {
                document.getElementById('lightboxModal').style.display = "none";
            }
        }

        function openLightbox(src, caption) {
            var lightbox = document.getElementById('lightboxModal');
            var img = document.getElementById('lightboxImg');
            var cap = document.getElementById('lightboxCaption');

            lightbox.style.display = "flex";
            img.src = src;
            cap.textContent = caption;
        }
    </script>
@endpush