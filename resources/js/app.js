const markPageReady = () => {
    window.requestAnimationFrame(() => {
        document.body.classList.add('page-ready');
    });
};

const initializeEventWizard = () => {
    const wizard = document.querySelector('[data-event-wizard]');

    if (! wizard) {
        return;
    }

    const stepOrder = JSON.parse(wizard.dataset.stepOrder || '[]');
    const sections = Array.from(wizard.querySelectorAll('[data-wizard-step]'));
    const triggers = Array.from(wizard.querySelectorAll('[data-step-trigger]'));
    const nextButton = wizard.querySelector('[data-step-next]');
    const backButton = wizard.querySelector('[data-step-back]');
    const submitButton = wizard.querySelector('[data-step-submit]');
    const stepIndicator = wizard.querySelector('[data-wizard-step-indicator]');
    const stepInput = wizard.querySelector('[data-wizard-step-input]');
    const titleInput = wizard.querySelector('[data-slug-source]');
    const slugInput = wizard.querySelector('[data-slug-target]');

    if (! stepOrder.length || ! sections.length) {
        return;
    }

    const initialStep = wizard.dataset.initialStep;
    let currentIndex = Math.max(0, stepOrder.indexOf(initialStep));

    const focusFirstField = (section) => {
        const field = section?.querySelector('input, select, textarea');

        if (field) {
            field.focus({ preventScroll: true });
        }
    };

    const validateCurrentStep = () => {
        const currentSection = sections[currentIndex];
        const fields = Array.from(currentSection.querySelectorAll('input, select, textarea'));
        const invalidField = fields.find((field) => typeof field.reportValidity === 'function' && ! field.reportValidity());

        if (invalidField) {
            invalidField.focus();
            return false;
        }

        return true;
    };

    const syncStepUi = () => {
        sections.forEach((section, index) => {
            const active = index === currentIndex;
            section.hidden = ! active;
            section.classList.toggle('is-active', active);
        });

        triggers.forEach((trigger, index) => {
            const active = index === currentIndex;
            trigger.classList.toggle('is-active', active);
            trigger.setAttribute('aria-current', active ? 'step' : 'false');
        });

        if (backButton) {
            backButton.disabled = currentIndex === 0;
        }

        if (nextButton) {
            const isLast = currentIndex === stepOrder.length - 1;
            nextButton.hidden = isLast;
            nextButton.textContent = 'Langkah berikutnya';
        }

        if (submitButton) {
            submitButton.hidden = currentIndex !== stepOrder.length - 1;
        }

        if (stepIndicator) {
            stepIndicator.textContent = `Step ${currentIndex + 1} dari ${stepOrder.length}`;
        }

        if (stepInput) {
            stepInput.value = stepOrder[currentIndex];
        }

        focusFirstField(sections[currentIndex]);
    };

    triggers.forEach((trigger, index) => {
        trigger.addEventListener('click', () => {
            currentIndex = index;
            syncStepUi();
        });
    });

    nextButton?.addEventListener('click', () => {
        if (! validateCurrentStep()) {
            return;
        }

        if (currentIndex < stepOrder.length - 1) {
            currentIndex += 1;
            syncStepUi();
        }
    });

    backButton?.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex -= 1;
            syncStepUi();
        }
    });

    wizard.addEventListener('submit', (event) => {
        const invalidSectionIndex = sections.findIndex((section) => {
            const fields = Array.from(section.querySelectorAll('input, select, textarea'));

            return fields.some((field) => typeof field.checkValidity === 'function' && ! field.checkValidity());
        });

        if (invalidSectionIndex === -1) {
            return;
        }

        if (invalidSectionIndex !== currentIndex) {
            event.preventDefault();
            currentIndex = invalidSectionIndex;
            syncStepUi();
            validateCurrentStep();
        }
    });

    if (titleInput && slugInput) {
        const slugify = (value) => value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        let lastAutoSlug = slugify(titleInput.value || '');

        titleInput.addEventListener('input', () => {
            const nextSlug = slugify(titleInput.value || '');
            const slugWasAuto = slugInput.value.trim() === '' || slugInput.value === lastAutoSlug;

            if (! slugWasAuto) {
                return;
            }

            slugInput.value = nextSlug;
            lastAutoSlug = nextSlug;
        });
    }

    syncStepUi();
};

const initializeInvitationAudio = () => {
    const audio = document.querySelector('[data-invitation-audio]');
    const toggle = document.querySelector('[data-audio-toggle]');

    if (! audio || ! toggle) {
        return;
    }

    audio.volume = 0.65;

    const updateToggle = () => {
        const muted = audio.muted;
        toggle.setAttribute('aria-pressed', muted ? 'true' : 'false');
        toggle.querySelector('[data-audio-icon]').textContent = muted ? 'volume_off' : 'volume_up';
        toggle.querySelector('[data-audio-label]').textContent = muted ? 'Nyalakan musik' : 'Mute musik';
    };

    const tryPlay = () => {
        const playback = audio.play();

        if (playback && typeof playback.catch === 'function') {
            playback.catch(() => {
                document.addEventListener('click', tryPlay, { once: true });
                document.addEventListener('touchstart', tryPlay, { once: true });
            });
        }
    };

    toggle.addEventListener('click', () => {
        audio.muted = ! audio.muted;
        if (! audio.paused) {
            updateToggle();
            return;
        }

        tryPlay();
        updateToggle();
    });

    updateToggle();
    tryPlay();
};

const initializeLeafletMaps = () => {
    if (typeof window.L === 'undefined') {
        return;
    }

    document.querySelectorAll('[data-map-picker]').forEach((container) => {
        if (container.dataset.mapReady === 'true') {
            return;
        }

        const wrapper = container.closest('[data-map-picker-wrapper]');
        const latInput = wrapper?.querySelector('[data-map-latitude]');
        const lngInput = wrapper?.querySelector('[data-map-longitude]');
        const urlInput = wrapper?.querySelector('[data-map-url]');
        const fallbackLat = Number.parseFloat(latInput?.value || '-6.2088');
        const fallbackLng = Number.parseFloat(lngInput?.value || '106.8456');
        const initialLat = Number.isFinite(fallbackLat) ? fallbackLat : -6.2088;
        const initialLng = Number.isFinite(fallbackLng) ? fallbackLng : 106.8456;

        const map = window.L.map(container, {
            scrollWheelZoom: false,
        }).setView([initialLat, initialLng], 14);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const marker = window.L.marker([initialLat, initialLng], {
            draggable: true,
        }).addTo(map);

        const syncInputs = (lat, lng) => {
            if (latInput) {
                latInput.value = Number.parseFloat(lat).toFixed(6);
            }

            if (lngInput) {
                lngInput.value = Number.parseFloat(lng).toFixed(6);
            }

            if (urlInput && ! urlInput.value.trim()) {
                urlInput.value = `https://www.google.com/maps?q=${lat},${lng}`;
            }
        };

        marker.on('dragend', () => {
            const position = marker.getLatLng();
            syncInputs(position.lat, position.lng);
        });

        map.on('click', (event) => {
            marker.setLatLng(event.latlng);
            syncInputs(event.latlng.lat, event.latlng.lng);
        });

        syncInputs(initialLat, initialLng);
        container.dataset.mapReady = 'true';
    });

    document.querySelectorAll('[data-invitation-map]').forEach((container) => {
        if (container.dataset.mapReady === 'true') {
            return;
        }

        const lat = Number.parseFloat(container.dataset.lat || '');
        const lng = Number.parseFloat(container.dataset.lng || '');

        if (! Number.isFinite(lat) || ! Number.isFinite(lng)) {
            return;
        }

        const map = window.L.map(container, {
            scrollWheelZoom: false,
            dragging: true,
            zoomControl: false,
        }).setView([lat, lng], 15);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        window.L.marker([lat, lng]).addTo(map);
        container.dataset.mapReady = 'true';
    });
};

const initializeOccasionFields = () => {
    const form = document.querySelector('[data-event-wizard]');
    const select = form?.querySelector('[data-occasion-select]');

    if (! form || ! select) {
        return;
    }

    let meta = {};

    try {
        meta = JSON.parse(form.dataset.occasionMeta || '{}');
    } catch {
        meta = {};
    }

    const displayLabel = form.querySelector('[data-occasion-display-label]');
    const displayInput = form.querySelector('[data-occasion-display-input]');
    const primaryLabel = form.querySelector('[data-occasion-primary-label]');
    const primaryInput = form.querySelector('[data-occasion-primary-input]');
    const secondaryLabel = form.querySelector('[data-occasion-secondary-label]');
    const secondaryInput = form.querySelector('[data-occasion-secondary-input]');

    const sync = () => {
        const selected = meta[select.value] || meta.wedding || {};

        if (displayLabel) {
            displayLabel.textContent = 'Judul utama undangan';
        }

        if (displayInput && selected.display_name_placeholder) {
            displayInput.placeholder = selected.display_name_placeholder;
        }

        if (primaryLabel && selected.form_primary_label) {
            primaryLabel.textContent = selected.form_primary_label;
        }

        if (primaryInput && selected.form_primary_label) {
            primaryInput.placeholder = selected.form_primary_label;
        }

        if (secondaryLabel && selected.form_secondary_label) {
            secondaryLabel.textContent = selected.form_secondary_label;
        }

        if (secondaryInput && selected.form_secondary_label) {
            secondaryInput.placeholder = selected.form_secondary_label;
        }
    };

    select.addEventListener('change', sync);
    sync();
};

const initializeAlbumUploadPreview = () => {
    const input = document.querySelector('[data-album-upload-input]');
    const previewList = document.querySelector('[data-album-preview-list]');

    if (! input || ! previewList || typeof window.DataTransfer === 'undefined') {
        return;
    }

    const maxDimension = 1800;
    const jpegQuality = 0.82;
    const outputMime = 'image/jpeg';

    const createPreviewCard = (file, dataUrl) => {
        const card = document.createElement('article');
        card.className = 'overflow-hidden rounded-[1.2rem] border border-outline-variant/18 bg-white shadow-[0_10px_26px_rgba(13,27,42,0.06)]';
        card.innerHTML = `
            <img src="${dataUrl}" alt="${file.name}" class="h-40 w-full object-cover">
            <div class="space-y-1 px-4 py-3">
                <p class="truncate text-sm font-semibold text-primary">${file.name}</p>
                <p class="text-xs text-on-surface-variant">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            </div>
        `;

        return card;
    };

    const loadImage = (file) => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
            const image = new Image();
            image.onload = () => resolve(image);
            image.onerror = reject;
            image.src = reader.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });

    const canvasToFile = (canvas, originalName) => new Promise((resolve) => {
        canvas.toBlob((blob) => {
            if (! blob) {
                resolve(null);
                return;
            }

            const outputName = originalName.replace(/\.(png|jpe?g|webp)$/i, '.jpg');
            resolve(new File([blob], outputName, { type: outputMime, lastModified: Date.now() }));
        }, outputMime, jpegQuality);
    });

    const compressImage = async (file) => {
        if (! file.type.startsWith('image/')) {
            return file;
        }

        const image = await loadImage(file);
        const ratio = Math.min(1, maxDimension / Math.max(image.width, image.height));
        const width = Math.max(1, Math.round(image.width * ratio));
        const height = Math.max(1, Math.round(image.height * ratio));
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        if (! context) {
            return file;
        }

        canvas.width = width;
        canvas.height = height;
        context.drawImage(image, 0, 0, width, height);

        const compressed = await canvasToFile(canvas, file.name);

        return compressed || file;
    };

    input.addEventListener('change', async () => {
        const files = Array.from(input.files || []);

        if (! files.length) {
            previewList.classList.add('hidden');
            previewList.innerHTML = '';
            return;
        }

        previewList.classList.remove('hidden');
        previewList.innerHTML = '';

        const processedFiles = [];

        for (const file of files) {
            const processed = await compressImage(file);
            processedFiles.push(processed);
            const dataUrl = await new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(processed);
            });

            previewList.appendChild(createPreviewCard(processed, dataUrl));
        }

        const transfer = new window.DataTransfer();
        processedFiles.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        markPageReady();
        initializeEventWizard();
        initializeOccasionFields();
        initializeAlbumUploadPreview();
        initializeInvitationAudio();
        initializeLeafletMaps();
    }, { once: true });
} else {
    markPageReady();
    initializeEventWizard();
    initializeOccasionFields();
    initializeAlbumUploadPreview();
    initializeInvitationAudio();
    initializeLeafletMaps();
}
