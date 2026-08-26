<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GlobalLoading
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | HANYA RESPONSE HTML
        |--------------------------------------------------------------------------
        */

        $contentType =
            $response->headers->get(
                'Content-Type'
            );

        if (
            !$contentType ||
            !str_contains(
                strtolower($contentType),
                'text/html'
            )
        ) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL HTML RESPONSE
        |--------------------------------------------------------------------------
        */

        $html =
            $response->getContent();

        if (
            !is_string($html) ||
            $html === ''
        ) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | JANGAN INJECT DUA KALI
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $html,
                'id="globalLoading"'
            )
        ) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | GLOBAL LOADING
        |--------------------------------------------------------------------------
        |
        | SATU MODEL UNTUK:
        |
        | - Semua menu
        | - Semua halaman
        | - Filter
        | - Simpan
        | - Update
        | - Hapus
        | - POST
        | - PUT
        | - PATCH
        | - DELETE
        |
        */

        $loading = <<<'HTML'

<style id="global-loading-style">

    .global-loading {
        position: fixed;

        inset: 0;

        z-index: 2147483647;

        display: none;

        align-items: center;
        justify-content: center;

        background:
            rgba(255, 255, 255, .60);

        backdrop-filter:
            blur(2px);

        -webkit-backdrop-filter:
            blur(2px);
    }


    .global-loading.active {
        display: flex;
    }


    .global-loading-box {
        text-align: center;
    }


    /* =====================================================
       KOTAK-KOTAK LOADING
    ===================================================== */

    .global-loading-track {
        display: flex;

        align-items: center;

        justify-content: center;

        gap: 5px;

        height: 18px;
    }


    .global-loading-track span {
        width: 7px;
        height: 7px;

        flex: 0 0 7px;

        display: block;

        border-radius: 1px;

        background: #9ca3af;

        opacity: .25;

        animation:
            globalLoadingMove
            .9s
            ease-in-out
            infinite;
    }


    .global-loading-track
    span:nth-child(1) {
        animation-delay: 0s;
    }


    .global-loading-track
    span:nth-child(2) {
        animation-delay: .10s;
    }


    .global-loading-track
    span:nth-child(3) {
        animation-delay: .20s;
    }


    .global-loading-track
    span:nth-child(4) {
        animation-delay: .30s;
    }


    .global-loading-track
    span:nth-child(5) {
        animation-delay: .40s;
    }


    @keyframes globalLoadingMove {

        0% {
            transform:
                translateX(0);

            opacity: .25;
        }


        50% {
            transform:
                translateX(7px);

            opacity: 1;
        }


        100% {
            transform:
                translateX(0);

            opacity: .25;
        }

    }


    .global-loading-text {
        margin-top: 8px;

        font-size: 11px;

        line-height: 1.2;

        font-weight: 400;

        color: #6b7280;

        text-align: center;
    }

</style>


<div
    id="globalLoading"
    class="global-loading"
    aria-hidden="true"
>

    <div
        class="global-loading-box"
    >

        <div
            class="global-loading-track"
        >

            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>

        </div>


        <div
            id="globalLoadingText"
            class="global-loading-text"
        >
            Memuat...
        </div>

    </div>

</div>


<script id="global-loading-script">

(function () {

    /*
    |--------------------------------------------------------------------------
    | CEGAH SCRIPT TERPASANG DUA KALI
    |--------------------------------------------------------------------------
    */

    if (
        window.__GLOBAL_LOADING_INITIALIZED__
    ) {
        return;
    }


    window.__GLOBAL_LOADING_INITIALIZED__ =
        true;


    /*
    |--------------------------------------------------------------------------
    | AMBIL ELEMENT
    |--------------------------------------------------------------------------
    */

    function getLoading()
    {
        return document.getElementById(
            'globalLoading'
        );
    }


    function getLoadingText()
    {
        return document.getElementById(
            'globalLoadingText'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN LOADING
    |--------------------------------------------------------------------------
    */

    function showLoading(
        text = 'Memuat...'
    ) {

        const loading =
            getLoading();


        const loadingText =
            getLoadingText();


        if (!loading) {
            return;
        }


        if (loadingText) {

            loadingText.textContent =
                text;

        }


        loading.classList.add(
            'active'
        );


        loading.setAttribute(
            'aria-hidden',
            'false'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SEMBUNYIKAN LOADING
    |--------------------------------------------------------------------------
    */

    function hideLoading()
    {

        const loading =
            getLoading();


        if (!loading) {
            return;
        }


        loading.classList.remove(
            'active'
        );


        loading.setAttribute(
            'aria-hidden',
            'true'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GLOBAL FUNCTION
    |--------------------------------------------------------------------------
    */

    window.showGlobalLoading =
        showLoading;


    window.hideGlobalLoading =
        hideLoading;


    /*
    |--------------------------------------------------------------------------
    | SEMUA LINK / MENU
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            const link =
                event.target.closest(
                    'a'
                );


            if (!link) {
                return;
            }


            const href =
                link.getAttribute(
                    'href'
                );


            if (
                !href ||
                href === '#' ||
                href.startsWith(
                    'javascript:'
                )
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | CTRL / SHIFT / ALT / CMD
            |----------------------------------------------------------------------
            */

            if (
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey ||
                event.metaKey
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | TARGET BARU
            |----------------------------------------------------------------------
            */

            if (
                link.target &&
                link.target !== '_self'
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | DOWNLOAD
            |----------------------------------------------------------------------
            */

            if (
                link.hasAttribute(
                    'download'
                )
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | AI PANEL
            |----------------------------------------------------------------------
            */

            if (
                link.closest(
                    '#aiAssistantPanel'
                )
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | PASTIKAN INTERNAL
            |----------------------------------------------------------------------
            */

            try {

                const url =
                    new URL(
                        href,
                        window.location.href
                    );


                if (
                    url.origin !==
                    window.location.origin
                ) {
                    return;
                }

            }
            catch (error) {

                return;

            }


            /*
            |----------------------------------------------------------------------
            | SEMUA MENU = LOADING YANG SAMA
            |----------------------------------------------------------------------
            */

            showLoading(
                'Memuat...'
            );

        },
        true
    );


    /*
    |--------------------------------------------------------------------------
    | SEMUA FORM
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'submit',
        function (event) {

            const form =
                event.target;


            if (
                !form ||
                form.tagName !== 'FORM'
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | FORM YANG DIKECUALIKAN
            |----------------------------------------------------------------------
            */

            if (
                form.hasAttribute(
                    'data-no-loading'
                )
            ) {
                return;
            }


            if (
                form.dataset.ajax ===
                'true'
            ) {
                return;
            }


            /*
            |----------------------------------------------------------------------
            | METHOD DEFAULT
            |----------------------------------------------------------------------
            */

            let method =
                (
                    form.getAttribute(
                        'method'
                    )
                    || 'GET'
                ).toUpperCase();


            /*
            |----------------------------------------------------------------------
            | LARAVEL METHOD OVERRIDE
            |----------------------------------------------------------------------
            */

            const methodOverride =
                form.querySelector(
                    'input[name="_method"]'
                );


            if (methodOverride) {

                const override =
                    String(
                        methodOverride.value
                    ).toUpperCase();


                if (
                    override === 'PUT' ||
                    override === 'PATCH' ||
                    override === 'DELETE'
                ) {

                    method =
                        override;

                }

            }


            /*
            |----------------------------------------------------------------------
            | GET / FILTER
            |----------------------------------------------------------------------
            */

            if (
                method === 'GET'
            ) {

                showLoading(
                    'Memuat...'
                );

                return;

            }


            /*
            |----------------------------------------------------------------------
            | POST / SIMPAN
            |----------------------------------------------------------------------
            */

            if (
                method === 'POST'
            ) {

                showLoading(
                    'Menyimpan...'
                );

            }


            /*
            |----------------------------------------------------------------------
            | PUT / PATCH / UPDATE
            |----------------------------------------------------------------------
            */

            else if (
                method === 'PUT' ||
                method === 'PATCH'
            ) {

                showLoading(
                    'Memperbarui...'
                );

            }


            /*
            |----------------------------------------------------------------------
            | DELETE / HAPUS
            |----------------------------------------------------------------------
            */

            else if (
                method === 'DELETE'
            ) {

                showLoading(
                    'Menghapus...'
                );

            }


            /*
            |----------------------------------------------------------------------
            | LAINNYA
            |----------------------------------------------------------------------
            */

            else {

                showLoading(
                    'Memproses...'
                );

            }


            /*
            |----------------------------------------------------------------------
            | CEGAH DOUBLE SUBMIT
            |----------------------------------------------------------------------
            */

            if (
                form.dataset.submitting ===
                'true'
            ) {

                event.preventDefault();

                return;

            }


            form.dataset.submitting =
                'true';


            /*
            |----------------------------------------------------------------------
            | DISABLE TOMBOL SIMPAN
            |----------------------------------------------------------------------
            */

            form
                .querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                )
                .forEach(
                    function (button) {

                        button.disabled =
                            true;

                    }
                );

        },
        true
    );


    /*
    |--------------------------------------------------------------------------
    | HALAMAN SELESAI
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'load',
        function () {

            hideLoading();

        }
    );


    window.addEventListener(
        'pageshow',
        function () {

            hideLoading();

        }
    );


})();

</script>

HTML;


        /*
        |--------------------------------------------------------------------------
        | MASUKKAN SEBELUM </body>
        |--------------------------------------------------------------------------
        */

        $position =
            strripos(
                $html,
                '</body>'
            );


        if (
            $position !== false
        ) {

            $html =
                substr_replace(
                    $html,
                    $loading,
                    $position,
                    0
                );

        }
        else {

            $html .= $loading;

        }


        /*
        |--------------------------------------------------------------------------
        | SET RESPONSE
        |--------------------------------------------------------------------------
        */

        $response->setContent(
            $html
        );


        return $response;
    }
}