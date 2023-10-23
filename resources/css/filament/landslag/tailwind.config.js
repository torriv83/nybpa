import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Landslag/**/*.php',
        './resources/views/filament/landslag/**/*.blade.php',
        './resources/views/livewire/landslag/**/*.blade.php',
        './app/helpers.php',
        './vendor/filament/**/*.blade.php',
    ],
}
