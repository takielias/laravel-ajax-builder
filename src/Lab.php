<?php

namespace Takielias\Lab;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Takielias\Lab\Enums\AlertType;

class Lab
{
    // Build ajax response
    protected array $responseData = [];
    protected int $status = 200;
    protected string $message = '';
    protected string $viewPath = '';
    protected ?View $view = null;
    protected ?View $alert = null;
    protected mixed $iconClass = null;
    protected array $viewData = [];

    public function __construct()
    {
        $this->responseData['fade_out'] = true;
        $this->responseData['fade_out_time'] = config('lab.fade_out_time', '3000');
        $this->responseData['redirect_delay'] = config('lab.redirect_delay', 1500);
        $this->responseData['scroll_to_top'] = false;
        $this->responseData['top_validation_error'] = false;
        $this->responseData['individual_validation_error'] = true;
        $this->responseData['submit_button_label'] = null;
        return $this;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function enableTopValidationError(): static
    {
        $this->responseData['top_validation_error'] = true;
        return $this;
    }

    public function disableIndividualValidationError(): static
    {
        $this->responseData['individual_validation_error'] = false;
        return $this;
    }

    public function setViewPath(string $path): static
    {
        $this->viewPath = $path;
        return $this;
    }

    public function setSubmitButtonLabel(string $label): static
    {
        $this->responseData['submit_button_label'] = $label;
        return $this;
    }

    public function getViewPath(): ?string
    {
        return $this->viewPath;
    }

    public function setViewData(array $data): static
    {
        $this->viewData = array_merge($this->viewData, $data);
        return $this;
    }

    public function getViewData(): ?array
    {
        return $this->viewData;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        if ($message !== null) {
            $this->responseData['message'] = $message;
            $this->setViewData(['message' => $message]);
        }
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setView($view): static
    {
        if ($view) {
            $this->view = $view;
        }
        return $this;
    }

    public function renderView(): static
    {
        $this->responseData['view'] = view($this->getViewPath(), $this->getViewData())->render();
        return $this;
    }

    public function setData(array $data): static
    {
        if (!isset($this->responseData['data'])) {
            $this->responseData['data'] = [];
        }

        // Merge the data arrays:
        $this->responseData['data'] = array_merge($this->responseData['data'], $data);

        return $this;
    }

    public function setAlert($alert): static
    {
        if ($alert) {
            $this->alert = $alert;
        }
        return $this;
    }

    public function renderAlert(): static
    {
        $this->responseData['alert'] = view($this->getViewPath(), $this->getViewData())->render();
        return $this;
    }

    public function setAlertView($type): static
    {
        $this->setViewPath('lab-alert::' . $type);
        return $this;
    }

    public function setIconClass($iconClass): static
    {
        if ($iconClass) {
            $this->iconClass = $iconClass;
            $this->setViewData(['icon' => $this->getIcon()]);
        }
        return $this;
    }

    public function getIcon()
    {
        return $this->iconClass;
    }

    public function setRedirect($redirect): static
    {
        if ($redirect) {
            $this->responseData['redirect'] = $redirect;
        }
        return $this;
    }

    public function disableFadeOut(): static
    {
        $this->responseData['fade_out'] = false;
        return $this;
    }

    public function enableScrollToTop(): static
    {
        $this->responseData['scroll_to_top'] = true;
        return $this;
    }

    public function setFadeOutTime(int $time_out): static
    {
        $this->responseData['fade_out_time'] = $time_out;
        return $this;
    }

    public function setRedirectDelay(int $delay): static
    {
        $this->responseData['redirect_delay'] = $delay;
        return $this;
    }

    public function setInfo($message = "Info !!!"): static
    {
        $icon = 'ti ti-info-circle';
        $this->setIconClass($icon);
        $this->setMessage($message);
        $this->setAlertView(AlertType::info->value);
        return $this;
    }

    public function setSuccess($message = "Success !!!"): static
    {
        $icon = 'ti ti-check';
        $this->setIconClass($icon);
        $this->setMessage($message);
        $this->setAlertView(AlertType::success->value);
        return $this;
    }

    public function setWarning($message = "Warning !!!"): static
    {
        $icon = 'ti ti-exclamation-circle';
        $this->setIconClass($icon);
        $this->setMessage($message);
        $this->setAlertView(AlertType::warning->value);
        return $this;
    }

    public function setDanger($message = "Danger !!!"): static
    {
        $icon = 'ti ti-alert-triangle';
        $this->setIconClass($icon);
        $this->setMessage($message);
        $this->setAlertView(AlertType::danger->value);
        return $this;
    }

    public function setValidationAlertView($validator): static
    {
        $icon = 'ti ti-alert-triangle';
        $this->setIconClass($icon);
        $this->setViewPath('lab-alert::' . AlertType::validationError->value);
        $this->setViewData(['errors' => $validator->errors()]);
        return $this;
    }

    public function setValidationError($validator): static
    {
        $icon = 'ti ti-alert-triangle';
        $this->setIconClass($icon);
        $this->setValidationAlertView($validator);
        $this->setMessage('Validation Error.')
            ->setData(['errors' => $validator->errors()->messages()]);
        return $this;
    }

    public function toJsonResponse(): JsonResponse
    {
        $this->renderView();
        $this->renderAlert();
        return response()->json($this->responseData, $this->status);
    }
}
