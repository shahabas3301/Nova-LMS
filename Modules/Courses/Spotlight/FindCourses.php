<?php

namespace Modules\Courses\Spotlight;

use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\Spotlight;
use Nwidart\Modules\Facades\Module;
use Illuminate\Http\Request;

class FindCourses extends SpotlightCommand
{
    /**
     * This is the name of the command that will be shown in the Spotlight component.
     */
    protected string $name = 'Find Courses';

    /**
     * This is the description of your command which will be shown besides the command name.
     */
    protected string $description = 'Redirect to user course search page';

    /**
     * You can define any number of additional search terms (also known as synonyms)
     * to be used when searching for this command.
     */
    protected array $synonyms = ['courses', 'course', 'all courses', 'find courses', 'course search', 'course listing', 'search courses'];

    /**
     * When all dependencies have been resolved the execute method is called.
     * You can type-hint all resolved dependency you defined earlier.
     */
    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('courses.search-courses'));
    }
    /**
     * You can provide any custom logic you want to determine whether the
     * command will be shown in the Spotlight component. If you don't have any
     * logic you can remove this method. You can type-hint any dependencies you
     * need and they will be resolved from the container.
     */
    public function shouldBeShown(Request $request): bool
    {
        if( Module::has('courses') && Module::isEnabled('courses')) {
            return $request->user()->role == 'student';
        }

        return false;
    }
}
