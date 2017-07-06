# amplify-actions
Plug-in to embed actions from the Amplify back-end into a WordPress site.

## Usage

    amplify_actions_all('CA', 12);
    
This will produce a `div` element containing a list of all actions, with their call scripts and background info, for that state and district.

The content is sourced from wp-content/amplify-actions-$state$district.json, which you must create/update yourself. Our plan is to have a cron job that downloads that content from Amplify to that file on a reasonable schedule.
