<?php
namespace Motwreen\Translation\Http\Controllers;

use Motwreen\Translation\Models\Locale;
use Illuminate\Http\Request;
use Motwreen\Translation\Services\LangFilesService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TranslationController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $locales = Locale::paginate(10);
        return view('Translation::index',compact('locales'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('Translation::create');
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'iso'=>'required|max:2|unique:locales,iso',
        ]);

        $locale = new Locale;
        $locale->name    = $request->get('name');
        $locale->iso     = $request->get('iso');
        $locale->default = Locale::count() == 0;
        $locale->save();

        $files = new LangFilesService();
        $files->createNewLangFilesFromDefault($locale->iso);

        return redirect(route('translation.index'))->with(['success'=>'Locale Created Successfully']);
    }

    public function show($locale)
    {
        $locale = Locale::findOrFail($locale);

        $files = new LangFilesService();
        $files->createNewLangFilesFromDefault($locale->iso);

        $files = File::files(resource_path('lang/'.$locale->iso));
        $files_in_dir['new_file'] = 'Create New file';
        foreach ($files as $file)
            $files_in_dir[$file->getFilename()] = Str::ucfirst($file->getFilename());

        $files_in_dir = str_replace('.php','',$files_in_dir);
        return view('Translation::show',compact('locale','files_in_dir'));
    }

    public function destroy($locale)
    {
        $locale = Locale::findOrFail($locale);
        $locale->delete();
        $files = new LangFilesService();
        $files->deleteDirectory($locale->iso);
        return redirect(route('translation.index'))->with(['success'=>'Locale deleted Successfully']);
    }

    /**
     * @param Locale $locale
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function saveTranslations(Locale $locale, Request $request)
    {

        $this->validate($request,[
            'file'=>'required',
        ]);

        if($request->get('file') === 'new_file'){
            $request->merge(['file'=>strtolower(str_replace('.php','',$request->get('new_file_name')).'.php')]);
            $this->validate($request,[
                'new_file_name'=>'required|alpha_dash',
            ]);
            $filename = str_replace('.php','',$request->get('new_file_name')).'.php';
            if(File::exists(resource_path('lang/'.$locale->iso.'/'.$filename) ))
                return back()->withErrors(['new_file_name'=>'File already exists'])->withInput(['file'=>'new_file','new_file_name'=>$request->get('new_file_name')]);
        }

        $inputs = transformUnderscoresToDotsInQueryString(file_get_contents('php://input'));
        $transformedArray = [];
        foreach ($inputs as $key=>$value){
            Arr::set($transformedArray, $key, $value);
        }
        $newKeysToAddInBoathFiles = $transformedArray['newkey']??[];
        unset($transformedArray['file'],$transformedArray['new_file_name'],$transformedArray['newkey']);

        $filesList = str_replace(resource_path().'/lang/','',File::directories(resource_path().'/lang/'));
        $filesService = new LangFilesService();
//        if(in_array($locale->iso,$filesList)){
//            $filesService->updateLangFile($locale->iso,$request->get('file'),$transformedArray);
//        }

        if(count($newKeysToAddInBoathFiles) !== 0){
            if($locale->iso != $filesService->default_lang)
                $filesService->updateLangFile($filesService->default_lang,$request->get('file'),$newKeysToAddInBoathFiles['default']);
            $filesService->updateLangFile($locale->iso,$request->get('file'),$newKeysToAddInBoathFiles['other']);
        }

        return redirect(route('translation.show',[$locale]))->with(['success'=>'Your Translations saved successfully']);
    }

    public function readLangFileAjax(Request $request)
    {
        $locale = $request->get('locale');
        $filename = $request->get('file');

        $files = new LangFilesService();
        $data = $files->readArrayFromFile($locale,$filename);
        return response()->json($data);
    }

    public function validateNewFileName(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'new_file_name'=>'required|alpha_dash',
        ]);

        if($validator->fails()){
            return response()->json(['success'=>false,'errors'=>$validator->errors()->get('new_file_name')]);
        }

        $filename = str_replace('.php','',$request->get('new_file_name')).'.php';
        if(File::exists(resource_path('lang/'.$request->get('lang').'/'.$filename) )){
            return response()->json(['success'=>false,'errors'=>['File already exists']]);
        }
        return response()->json(['success'=>true]);
    }
}
