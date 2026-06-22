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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        markPageReady();
        initializeEventWizard();
    }, { once: true });
} else {
    markPageReady();
    initializeEventWizard();
}
