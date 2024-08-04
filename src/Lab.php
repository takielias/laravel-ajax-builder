<?php

namespace Takielias\Lab;

use Illuminate\Http\JsonResponse;

class Lab
{
    // Build ajax response
    protected array $responseData = [];
    protected int $status = 200;
    protected mixed $iconClass = null;

    public function __construct()
    {
        $this->responseData['fade_out'] = true;
        $this->responseData['fade_out_time'] = 3000;
        $this->responseData['redirect_delay'] = 1500;
        $this->responseData['scroll_to_top'] = false;
        return $this;
    }

    public function setStatus($status): static
    {
        $this->status = $status;
        return $this;
    }

    public function setMessage($message): static
    {
        if ($message) {
            $this->responseData['message'] = $message;
        }
        return $this;
    }

    public function setView($view): static
    {
        if ($view) {
            $this->responseData['view'] = $view->render();
        }
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
            $this->responseData['alert'] = $alert->render();
        }
        return $this;
    }

    public function setIconClass($iconClass): static
    {
        if ($iconClass) {
            $this->iconClass = $iconClass;
        }
        return $this;
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

    public function setSuccess($message = "Success !!!"): static
    {
        $icon = 'ti ti-check';
        $alert = view('lab-alert::success', ['message' => $message, 'icon' => $this->iconClass ?? $icon]);
        if ($alert) {
            $this->responseData['alert'] = $alert->render();
        }
        return $this;
    }

    public function setInfo($message = "Info !!!"): static
    {
        $icon = 'ti ti-info-circle';
        $alert = view('lab-alert::info', ['message' => $message, 'icon' => $this->iconClass ?? $icon]);
        if ($alert) {
            $this->responseData['alert'] = $alert->render();
        }
        return $this;
    }

    public function setWarning($message = "Warning !!!"): static
    {
        $icon = 'ti ti-exclamation-circle';
        $alert = view('lab-alert::warning', ['message' => $message, 'icon' => $this->iconClass ?? $icon]);
        if ($alert) {
            $this->responseData['alert'] = $alert->render();
        }
        return $this;
    }

    public function setDanger($message = "Danger !!!"): static
    {
        $icon = 'ti ti-alert-triangle';
        $alert = view('lab-alert::danger', ['message' => $message, 'icon' => $this->iconClass ?? $icon]);
        if ($alert) {
            $this->responseData['alert'] = $alert->render();
        }
        return $this;
    }

    public function setValidationError($validator): static
    {
        $icon = 'ti ti-alert-triangle';
        $alert = view('lab-alert::validation-error', ['errors' => $validator->errors(), 'icon' => $this->iconClass ?? $icon]);
        $this->setMessage('Validation Error.')
            ->setData(['errors' => $validator->errors()->messages()])
            ->setAlert($alert);
        return $this;
    }

    public function toJsonResponse(): JsonResponse
    {
        return response()->json($this->responseData, $this->status);
    }
}
