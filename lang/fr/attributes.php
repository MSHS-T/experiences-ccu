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
    'role'       => 'Rôle',

    // Equipment
    'quantity' => 'Quantité',

    // Plateau
    'manager'    => 'Gestionnaire',
    'equipments' => 'Équipements',

    // Manipulation
    'plateau'         => 'Plateau',
    'duration'        => 'Durée du créneau',
    'target_slots'    => 'Créneaux',
    'slot_count'      => 'Nombre de créneaux',
    'start_date'      => 'Date de début',
    'end_date'        => 'Date de fin',
    'dates'           => 'Dates',
    'location'        => 'Emplacement',
    'available_hours' => 'Horaires',
    'requirements'    => 'Critères d\'inclusion',
    'published'       => 'Publié ?',

    // Attribution
    'manipulation_manager' => 'Responsable de manipulation',
    'allowed_halfdays'     => 'Demi-journées autorisées',
    'creator'              => 'Créateur',
    'attributions'         => 'Attributions',

    // Booking
    'birthdate' => 'Date de Naissance',

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
    'booking_cancellation_delay' => 'Délai de prévenance pour l\'annulation d\'une réservation',
    'booking_confirmation_delay' => 'Délai maximal pour la confirmation d\'une réservation',
    'booking_opening_delay'      => 'Délai d\'ouverture des inscriptions avant début de la manipulation',
    'email_reminder_delay'       => 'Délai avant réservation pour envoi du premier rappel par e-mail',
    'manipulation_overbooking'   => 'Pourcentage de surréservation des créneaux de manipulation',
    'presentation_text'          => 'Texte de présentation du site (visible sur la page d\'accueil)',
    'access_instructions'        => 'Instructions d\'accès',

    // Roles
    'roles' => [
        'administrator'        => 'Administrateur',
        'plateau_manager'      => 'Responsable Plateau',
        'manipulation_manager' => 'Responsable Manipulation',
    ],
];
