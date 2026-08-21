<?php
/**
 * Default tool-page assistant.
 */
return array(
	'id'                   => 'general',
	'name'                 => 'دستیار ابزار',
	'description'          => 'Helps visitors use the on-page calculator.',
	'default_agent'        => 'general',
	'default_agent_name'   => 'Assistants',
	'collaboration_mode'   => 'single',
	'thinking_enabled'     => true,
	'initial_message'      => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
	'free_tier_limit'      => 10,
	'signed_in_limit'      => 100,
	'cost_per_message'     => 0,
	'recommended_agents'   => array( 'general', 'stats', 'math' ),
	'skills'               => array(),
	'context'              => array( 'purpose' => 'tool help' ),
);
