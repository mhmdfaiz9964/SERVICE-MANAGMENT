@extends('layouts.app')

@section('title', 'App Settings')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-gear me-2"></i>
                <h4 class="mb-0 text-white">App Settings</h4>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.app_settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" name="site_name" id="site_name" class="form-control" 
                                       value="{{ old('site_name', $settings->site_name ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="meta_name" class="form-label">Meta Name</label>
                                <input type="text" name="meta_name" id="meta_name" class="form-control" 
                                       value="{{ old('meta_name', $settings->meta_name ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="meta_tag" class="form-label">Meta Tag</label>
                                <input type="text" name="meta_tag" id="meta_tag" class="form-control" 
                                       value="{{ old('meta_tag', $settings->meta_tag ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $settings->description ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="copyright_message" class="form-label">Copyright Message</label>
                                <input type="text" name="copyright_message" id="copyright_message" class="form-control" 
                                       value="{{ old('copyright_message', $settings->copyright_message ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Logo</h5>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo Image</label>
                                <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                @if($settings->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset($settings->logo) }}" alt="Current Logo" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Social Links</h5>
                            <div id="social-links-container">
                                @if(old('social_links', $settings->social_links ?? []) && count(old('social_links', $settings->social_links ?? [])) > 0)
                                    @foreach(old('social_links', $settings->social_links ?? []) as $index => $link)
                                        <div class="input-group mb-2 social-link-group">
                                            <input type="url" name="social_links[]" class="form-control" placeholder="Social Link URL" value="{{ $link }}">
                                            <button type="button" class="btn btn-outline-danger remove-social" onclick="removeSocial(this)">Remove</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 social-link-group">
                                        <input type="url" name="social_links[]" class="form-control" placeholder="Social Link URL">
                                        <button type="button" class="btn btn-outline-danger remove-social d-none" onclick="removeSocial(this)">Remove</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-secondary" onclick="addSocialLink()">Add Social Link</button>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Splash Screen</h5>
                            <div class="mb-3">
                                <label for="splash_screen_image" class="form-label">Splash Screen Image</label>
                                <input type="file" name="splash_screen_image" id="splash_screen_image" class="form-control" accept="image/*" onchange="updateSplashPreview()">
                                @if($settings->splash_screen_image)
                                    <div class="mt-2">
                                        <img src="{{ asset($settings->splash_screen_image) }}" alt="Current Splash" id="current-splash-img" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="splash_screen_title" class="form-label">Splash Screen Title</label>
                                <input type="text" name="splash_screen_title" id="splash_screen_title" class="form-control" 
                                       value="{{ old('splash_screen_title', $settings->splash_screen_title ?? '') }}" onchange="updateSplashPreview()">
                            </div>

                            <div class="mb-3">
                                <label for="splash_screen_description" class="form-label">Splash Screen Description</label>
                                <textarea name="splash_screen_description" id="splash_screen_description" class="form-control" rows="2" onchange="updateSplashPreview()">{{ old('splash_screen_description', $settings->splash_screen_description ?? '') }}</textarea>
                            </div>

                            <!-- Real-time Splash Screen Preview -->
                            <div class="border rounded p-3 bg-light" style="min-height: 200px; position: relative;">
                                <h6 class="text-muted mb-2">Live Preview:</h6>
                                <div id="splash-preview" style="position: relative; width: 100%; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; text-align: center; border-radius: 8px;">
                                    <div id="splash-content">
                                        <img id="preview-splash-img" src="" alt="Splash Image" style="position: absolute; top: 20px; right: 20px; width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); display: none;">
                                        <h3 id="preview-title" style="margin: 0; font-size: 1.5rem;">{{ old('splash_screen_title', $settings->splash_screen_title ?? 'Welcome') }}</h3>
                                        <p id="preview-desc" style="margin: 10px 0 0 0; opacity: 0.9;">{{ old('splash_screen_description', $settings->splash_screen_description ?? 'Your app description here') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Home Banners</h5>
                            <div id="banners-container">
                                @if(old('home_banner', $settings->home_banner ?? []) && count(old('home_banner', $settings->home_banner ?? [])) > 0)
                                    @foreach(old('home_banner', $settings->home_banner ?? []) as $index => $banner)
                                        <div class="banner-group mb-4 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Banner Image {{ $index + 1 }}</label>
                                                    <input type="file" name="home_banner[{{ $index }}][image]" class="form-control" accept="image/*">
                                                    @if(isset($banner['image']))
                                                        <div class="mt-2">
                                                            <img src="{{ asset($banner['image']) }}" alt="Current Banner {{ $index + 1 }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                                            <input type="hidden" name="home_banner[{{ $index }}][image_old]" value="{{ $banner['image'] }}">
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" name="home_banner[{{ $index }}][title]" class="form-control" value="{{ $banner['title'] ?? '' }}">
                                                    <input type="hidden" name="home_banner[{{ $index }}][id]" value="{{ $banner['id'] ?? ($index + 1) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Subtitle</label>
                                                    <input type="text" name="home_banner[{{ $index }}][subtitle]" class="form-control" value="{{ $banner['subtitle'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeBanner(this)">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="banner-group mb-4 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Banner Image 1</label>
                                                <input type="file" name="home_banner[0][image]" class="form-control" accept="image/*">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Title</label>
                                                <input type="text" name="home_banner[0][title]" class="form-control">
                                                <input type="hidden" name="home_banner[0][id]" value="1">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Subtitle</label>
                                                <input type="text" name="home_banner[0][subtitle]" class="form-control">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger d-none" onclick="removeBanner(this)">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBanner()">Add Banner</button>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Initialize splash preview on load
        document.addEventListener('DOMContentLoaded', function() {
            updateSplashPreview();
        });

        function updateSplashPreview() {
            const title = document.getElementById('splash_screen_title').value || 'Welcome';
            const desc = document.getElementById('splash_screen_description').value || 'Your app description here';
            const fileInput = document.getElementById('splash_screen_image');
            const previewImg = document.getElementById('preview-splash-img');
            const currentImg = document.getElementById('current-splash-img');

            document.getElementById('preview-title').textContent = title;
            document.getElementById('preview-desc').textContent = desc;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else if (currentImg && currentImg.src) {
                previewImg.src = currentImg.src;
                previewImg.style.display = 'block';
            } else {
                previewImg.style.display = 'none';
            }
        }

        // Social Links Management
        let socialIndex = {{ count(old('social_links', $settings->social_links ?? [])) }};
        function addSocialLink() {
            const container = document.getElementById('social-links-container');
            const group = document.createElement('div');
            group.className = 'input-group mb-2 social-link-group';
            group.innerHTML = `
                <input type="url" name="social_links[]" class="form-control" placeholder="Social Link URL">
                <button type="button" class="btn btn-outline-danger remove-social" onclick="removeSocial(this)">Remove</button>
            `;
            container.appendChild(group);
            socialIndex++;
        }

        function removeSocial(button) {
            button.closest('.social-link-group').remove();
        }

        // Home Banners Management
        let bannerIndex = {{ count(old('home_banner', $settings->home_banner ?? [])) }};
        function addBanner() {
            const container = document.getElementById('banners-container');
            const group = document.createElement('div');
            group.className = 'banner-group mb-4 p-3 border rounded';
            group.innerHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Banner Image ${bannerIndex + 1}</label>
                        <input type="file" name="home_banner[${bannerIndex}][image]" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Title</label>
                        <input type="text" name="home_banner[${bannerIndex}][title]" class="form-control">
                        <input type="hidden" name="home_banner[${bannerIndex}][id]" value="${bannerIndex + 1}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="home_banner[${bannerIndex}][subtitle]" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger" onclick="removeBanner(this)">Remove</button>
                    </div>
                </div>
            `;
            container.appendChild(group);
            bannerIndex++;
        }

        function removeBanner(button) {
            button.closest('.banner-group').remove();
        }
    </script>
@endsection