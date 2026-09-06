{{--
    Every message on the site goes through SweetAlert.

    Include it once, just before the closing body tag, after the SweetAlert
    script has loaded. Pass swalToast => true for the admin panel, where a
    small corner toast is less annoying than a box you have to dismiss.
--}}
@php
    $swalToast = $swalToast ?? false;
    $swalSuccess = session('success');
    $swalError = session('error');

    $swalErrorList = '';

    if ($errors->any()) {
        $swalErrorList = '<ul style="text-align:left;margin:0;padding-left:1.1rem">';

        foreach ($errors->all() as $swalMessage) {
            $swalErrorList .= '<li>'.e($swalMessage).'</li>';
        }

        $swalErrorList .= '</ul>';
    }
@endphp

@if ($swalSuccess || $swalError || $swalErrorList)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal === 'undefined') {
                return;
            }

            var brand = '#14532d';

            @if ($swalSuccess)
                @if ($swalToast)
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json($swalSuccess),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                @else
                    Swal.fire({
                        icon: 'success',
                        title: 'Thank you',
                        text: @json($swalSuccess),
                        confirmButtonText: 'OK',
                        confirmButtonColor: brand
                    });
                @endif
            @elseif ($swalError)
                Swal.fire({
                    icon: 'error',
                    title: 'Sorry',
                    text: @json($swalError),
                    confirmButtonText: 'OK',
                    confirmButtonColor: brand
                });
            @elseif ($swalErrorList)
                Swal.fire({
                    icon: 'error',
                    title: 'Please check the form',
                    html: @json($swalErrorList),
                    confirmButtonText: 'OK',
                    confirmButtonColor: brand
                });
            @endif
        });
    </script>
@endif

{{-- Any form with data-confirm="..." asks first, using SweetAlert. --}}
<script>
    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (!form || !form.getAttribute) {
            return;
        }

        var message = form.getAttribute('data-confirm');

        if (!message || form.dataset.confirmed === 'yes' || typeof Swal === 'undefined') {
            return;
        }

        event.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: message,
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'yes';
                form.submit();
            }
        });
    }, true);
</script>
