import './bootstrap';

window.submitForm = async function(event, formId) {
    event.preventDefault();

    const form = document.getElementById(`dynamic-form-${formId}`);
    const messageDiv = document.getElementById(`form-message-${formId}`);
    const submitButton = form.querySelector('button[type="submit"]');

    messageDiv.classList.add('hidden');
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...';

    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        if (key !== '_hp_field' || value !== '') {
            data[key] = value;
        }
    });

    try {
        const response = await axios.post(`/api/v1/forms/${formId}/submit`, data);

        messageDiv.textContent = response.data.message || 'Form submitted successfully!';
        messageDiv.className = 'mt-4 rounded-md bg-green-50 p-4 text-green-700';
        messageDiv.classList.remove('hidden');

        form.reset();
    } catch (error) {
        let message = 'An error occurred. Please try again.';

        if (error.response?.data?.error) {
            message = error.response.data.error;
        }

        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            message = Object.values(errors).flat().join(' ');
        }

        messageDiv.textContent = message;
        messageDiv.className = 'mt-4 rounded-md bg-red-50 p-4 text-red-700';
        messageDiv.classList.remove('hidden');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Submit';
    }

    return false;
};
