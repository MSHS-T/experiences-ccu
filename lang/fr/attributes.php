<?php

return [
    // Generic
    'name'        => 'Nom',
    'type'        => 'Type',
    'description' => 'Description',
    'photos'      => 'Photos',
    'created_at'  => 'Création',
    'updated_at'  => 'Modification',

    // User
    'first_name' => 'Prénom',
    'last_name'  => 'Nom de famille',
    'email'      => 'Adresse e-mail',
    'role'       => 'Rôles',

    // Equipment
    'quantity' => 'Quantité',

    // Plateau
    'manager'    => 'Gestionnaire',
    'equipments' => 'Équipements',
    'color'      => 'Couleur',

    // Manipulation
    'plateau'              => 'Plateau',
    'duration'             => 'Durée du créneau',
    'target_slots'         => 'Créneaux',
    'slot_count'           => 'Nombre de créneaux',
    'generated_slot_count' => 'Nombre de créneaux générés',
    'start_date'           => 'Date de début',
    'end_date'             => 'Date de fin',
    'dates'                => 'Dates',
    'location'             => 'Emplacement',
    'available_hours'      => 'Horaires',
    'requirements'         => 'Critères d\'inclusion',
    'published'            => 'État de publication',
    'published_yes'        => 'Publiées seulement',
    'published_no'         => 'Non publiées seulement',
    'published_all'        => 'Tous',
    'archived'             => 'État d\'archivage',
    'archived_yes'         => 'Archivées seulement',
    'archived_no'          => 'Non archivées seulement',
    'archived_all'         => 'Tous',

    // Attribution
    'manipulation_manager'  => 'Responsable de manipulation',
    'manipulation_managers' => 'Responsables de manipulation',
    'allowed_halfdays'      => 'Demi-journées autorisées',
    'creator'               => 'Créateur',
    'attributions'          => 'Attributions',

    // Slot
    'start' => 'Début',
    'end'   => 'Fin',

    // Booking
    'confirmed' => 'Confirmé ?',
    'booked_at' => 'Date de réservation',

    // Hours
    'monday'    => 'Lundi',
    'tuesday'   => 'Mardi',
    'wednesday' => 'Mercredi',
    'thursday'  => 'Jeudi',
    'friday'    => 'Vendredi',
    'am'        => 'Matin',
    'pm'        => 'Après-midi',
    'start_am'  => 'Début de matinée',
    'end_am'    => 'Fin de matinée',
    'start_pm'  => 'Début d\'après-midi',
    'end_pm'    => 'Fin d\'après-midi',

    // Settings
    'booking_confirmation_delay' => 'Délai maximal pour la confirmation d\'une réservation',
    'booking_opening_delay'      => 'Délai d\'ouverture des inscriptions avant début de l\'expérience',
    'email_first_reminder_delay' => 'Délai avant réservation pour envoi du premier email de rappel',
    'email_last_reminder_delay'  => 'Délai avant réservation pour envoi du dernier email de rappel',
    'presentation_text'          => 'Texte de présentation du site (visible sur la page d\'accueil)',
    'access_instructions'        => 'Instructions d\'accès',

    // Roles
    'roles' => [
        'administrator'        => 'Administrateur',
        'plateau_manager'      => 'Responsable Plateau',
        'manipulation_manager' => 'Responsable Manipulation',
    ],
];
