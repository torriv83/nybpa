import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Privat/**/*.php',
        './resources/views/filament/privat/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
