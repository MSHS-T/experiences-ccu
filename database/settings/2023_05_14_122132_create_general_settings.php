<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.booking_cancellation_delay', 1);
        $this->migrator->add('general.booking_confirmation_delay', 24);
        $this->migrator->add('general.booking_opening_delay', 30);
        $this->migrator->add('general.email_reminder_delay', 7);
        $this->migrator->add('general.presentation_text', '<p>La plateforme CCU est un ensemble d’équipements scientifiques dédiés à la recherche sur le comportement humain. Elle permet l’acquisition de données physiologiques, comportementales et subjectives. Sa principale force est qu’elle permet aussi le couplage de ces données hétérogènes afin d’en dégager les possibles interactions.<br/>La plateforme s’adresse à tout type de participant et participante, du bébé jusqu’à la personne âgée, typique ou présentant une pathologie.</p>');
        $this->migrator->add('general.access_instructions', '<ul><li>Université Toulouse-Jean Jaurès</li><li>Maison de la Recherche</li><li>Plateforme CCU</li></ul>');
    }
};
