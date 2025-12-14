@props(['title', 'description', 'subtitle'])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
    <flux:subheading>{{ $subtitle }}</flux:subheading>
</div>
