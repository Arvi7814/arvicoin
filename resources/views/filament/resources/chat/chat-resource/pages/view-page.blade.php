<?php
/**
 * @var boolean $hideInfo
 * @var Chat $record
 * @var ChatMessage[] $messages
 * @var TemplateMessage[] $templates
 * @var Order[] $orders
 */

use App\Enum\ChatMessageTypeEnum;
use App\Enum\LangEnum;
use App\Helpers\MediaHelper;
use App\Models\Chat\Chat;
use App\Models\Chat\ChatMessage;
use App\Models\Order\Order;
use App\Models\TemplateMessage;

$currentUser = Auth::id();
$currentOrder = $record->order;
?>

<x-filament::page>
    <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet"/>
    <style>
        .other-orders {
            display: flex;
            overflow: scroll;

            margin-top: 5px !important;

            background-color: #f3f4f6ff;
        }

        .other-orders:hover {
            opacity: 30%;
        }

        .other-orders article {
            flex-shrink: 0;
            margin: 0 5px;
        }

        .messages {
            position: relative;
            display: flex;
            flex-direction: column;

            height: calc(100vh - 344px);
            min-height: 560px;
            overflow: scroll;
        }

        .messages > div:first-child {
            margin-top: auto;
        }

        .templates ul {
            display: flex;
            flex-wrap: wrap;
        }

        .templates li {
            margin: 2px;
            cursor: pointer;
        }

        .info {
            position: absolute;
            bottom: 0;
            left: 0;

            width: 100%;
            padding: 24px;

            display: block;
            border-radius: 10px;
            background-color: #ffffff;
        }

        .info.hide {
            display: none;
        }

        .info table {
            width: 100%;
            text-align: center;
        }
    </style>

    <div class="other-orders">
        @foreach($orders as $order)
            @include('components.order-preview', [
                    'order' => $order
            ])
        @endforeach
    </div>
    <section class="messages pr-2">
        @foreach($messages as $message)
            @continue($message->type === ChatMessageTypeEnum::TEXT && empty($message->content))
            @php $isUsersPage = $message->user_id === $currentUser; @endphp
            <div class="message relative @if($isUsersPage) text-right @endif" wire:key="{{$message->id}}"
                 style="margin-bottom: 10px;">
                <div @class([
                        'inline-block bg-white shadow rounded text-sm mr-6'
                    ]) style="margin-bottom: 5px;">
                    @if($message->type === ChatMessageTypeEnum::MEDIA)
                        @foreach($message->media as $media)
                            <div class="mb-2">
                                @if(MediaHelper::isPhoto($media) || MediaHelper::isAnimation($media))
                                    <img src="{{$media->getUrl()}}" alt="{{$media->name}}"
                                         style="max-width: 200px;"/>
                                @elseif(MediaHelper::isVideo($media))
                                    <video
                                        class="video-js"
                                        controls
                                        preload="auto"
                                        width="640"
                                        height="264"
                                        data-setup="{}"
                                    >
                                        <source src="{{$media->getFullUrl()}}" type="video/mp4"/>
                                    </video>
                                @elseif(MediaHelper::isAudio($media))
                                    <audio
                                        src="{{$media->getFullUrl()}}" controls
                                        style="width: 300px;"
                                    >
                                    </audio>
                                @else
                                    <a href="{{$media->getUrl()}}" target="_blank">
                                        <div class="flex items-center p-1">
                                        <span style="width: 5px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                              <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </span>
                                            <span class="block w-full text-center">{{$media->name}}</span>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                    <div class="px-3 py-1 flex">
                        @if($message->content)
                            <p class="mr-4">
                                {{ $message->content }}
                            </p>
                        @endif
                        @if($isUsersPage && !$message->deleted_at)
                            <button type="button" wire:click="deleteMessage({{$message->id}})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                <h3 class="text-gray-100" style="font-size: 12px;">
                    {{$message->user->first_name}}, {{$message->created_at->format('H:i, d.m.Y')}}
                </h3>
            </div>
        @endforeach

        <div class="templates">
            @foreach(LangEnum::cases() as $langEnum)
                @continue(in_array($langEnum, [
                LangEnum::EN
                ]))
                <ul class="d-flex flex-wrap">
                    <li class="text-dark p-1 font-bold">
                        {{$langEnum->value}}:
                    </li>
                    @foreach($templates as $template)
                        @php $templateMessage = $template->message[$langEnum->value] ?? '' @endphp
                        <li wire:click='sendTemplate("{{$templateMessage}}")'
                            class="text-white bg-primary-500 py-1 px-3 rounded font-bold"
                        >
                            {{$template->number}}
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>

        <div class="info @if($hideInfo) hide @endif">
            <table>
                <thead>
                <tr>
                    <th>@lang('fields.name')</th>
                    <th>@lang('fields.count')</th>
                    <th>@lang('fields.ordered_price')</th>
                    <th>@lang('fields.amount')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($currentOrder->orderProducts as $orderProduct)
                    <tr>
                        <td>{{$orderProduct->product->name}}</td>
                        <td>{{$orderProduct->count}}</td>
                        <td>{{$orderProduct->price}} {{$currentOrder->currency}}</td>
                        <td>{{$orderProduct->count * $orderProduct->price}} {{$currentOrder->currency}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if($errors->any())
        <div class="bg-white mt-4 p-2">
            {!! implode('<br/>', $errors->all()) !!}
        </div>
    @endif
    <form id="footer"
          wire:submit.prevent="submit"
          class="border-gray-100"
    >
        <label for="message-area" class="text-sm">@lang('fields.message')</label>
        <div class="flex items-center">
            <div class="w-full">
                <textarea id="message-area"
                          wire:model="message"
                          wire:keydown.enter="submit"
                          class="filament-forms-textarea-component block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300"
                          style="height: 50px"
                          name="message"
                ></textarea>
            </div>
            <button type="button" wire:click="toggleInfo" class="bg-primary-500 text-white p-2 rounded-lg ml-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
            <button id="media-btn" type="button" class="bg-primary-500 text-white p-2 rounded-lg ml-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            </button>
            <button wire:loading.remove wire:target="media" id="submit-btn" type="submit"
                    class="bg-primary-500 text-white p-2 rounded-lg ml-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>
        <input id="media" wire:model="media" type="file" name="media" style="display: none"/>
    </form>

    <script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>
    <script>
        document.getElementById('footer').scrollIntoView()
        document.getElementById('media-btn').addEventListener('click', () => {
            document.getElementById('media').click()
        })
        window.addEventListener('paste', function (event) {
            const files = (event.clipboardData || event.originalEvent.clipboardData).items
            const file = files[0] ?? null

            if (file && file.kind === 'file') {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file.getAsFile());
                document.getElementById('media').files = dataTransfer.files;
                document.getElementById('media').dispatchEvent(new Event('change'))
            }
        })
    </script>
</x-filament::page>
