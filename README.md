# amplify-actions
Plug-in to embed actions from the Amplify back-end into a WordPress site.

## Usage

    amplify_actions_all('CA', 12);
    
This will produce a `div` element containing a list of all actions, with their call scripts and background info, for that state and Congressional district.

    amplify_actions_one_moc('CA', 12, 12345);

This will produce a `div` element containing a list of all actions, with their call scripts and background info, for that Member of Congress, identified by the person ID that their actions are tagged with in the source data.

    amplify_actions_one_state_upper('CA', 11, 12345);

This will produce a `div` element containing a list of all actions, with their call scripts and background info, for that Member of a state's upper house (e.g., California State Senate), identified by the person ID that their actions are tagged with in the source data.

    amplify_actions_one_state_lower('CA', 19, 12345);

This will produce a `div` element containing a list of all actions, with their call scripts and background info, for that Member of a state's lower house (e.g., California State Assembly), identified by the person ID that their actions are tagged with in the source data.

The content is sourced from wp-content/amplify-actions-US-$state$district.json, wp-content/amplify-actions-$state-upper-$district.json, and wp-content/amplify-actions-$state-upper-$district.json, which you must create/update yourself. Our plan is to have a cron job that downloads that content from Amplify to that file on a reasonable schedule.

## Requirements

https://michelf.ca/projects/php-markdown/
