@extends('layouts.app')

@section('title', $ficha ? 'Editar ficha' : 'Asignar ficha')
@section('section', 'Fichas')
@section('heading', $ficha ? 'Editar ficha' : 'Asignar ficha')

@section('content')
    <div class="form-card">
        <div class="form-intro"><h2>{{ $ficha ? 'Editar ficha N.º '.$ficha->id : 'Asignar nueva ficha' }}</h2><p>Indique fecha y hora para ver los doctores disponibles y asignar la ficha al paciente.</p></div>
        <form method="POST" action="{{ $ficha ? route('fichas.update', $ficha) : route('fichas.store') }}">
            @csrf
            @if ($ficha) @method('PUT') @endif
            <div class="form-grid">
                <div class="field field-wide"><label for="paciente_id">Paciente</label><select id="paciente_id" name="paciente_id" required><option value="">Selecciona un paciente</option>@foreach ($pacientes as $paciente)<option value="{{ $paciente->id }}" @selected((string) old('paciente_id', $ficha?->paciente_id) === (string) $paciente->id)>{{ $paciente->nombre_completo }} · CI {{ $paciente->ci }}</option>@endforeach</select><x-error field="paciente_id" />@if ($pacientes->isEmpty())<small class="field-help">Primero debes <a href="{{ route('pacientes.create') }}">registrar un paciente</a>.</small>@endif</div>
                <div class="field"><label for="fecha">Fecha</label><input id="fecha" name="fecha" type="date" min="{{ $ficha && $ficha->fecha < now()->toDateString() ? $ficha->fecha : now()->toDateString() }}" value="{{ old('fecha', $ficha?->fecha) }}" required><x-error field="fecha" /></div>
                <div class="field"><label for="hora">Hora</label><input id="hora" name="hora" type="time" value="{{ old('hora', $ficha?->hora) }}" required><x-error field="hora" /></div>
                <div class="field field-wide"><label for="doctor_id">Doctor disponible</label><select id="doctor_id" name="doctor_id" required data-selected="{{ old('doctor_id', $ficha?->doctor_id) }}"><option value="">Selecciona fecha y hora primero</option>@foreach ($doctores as $doctor)<option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $ficha?->doctor_id) === (string) $doctor->id)>{{ $doctor->nombre_completo }} — {{ $doctor->especialidad }}</option>@endforeach</select><span class="field-help" id="disponibilidad" aria-live="polite">La disponibilidad se consulta al indicar fecha y hora.</span><x-error field="doctor_id" /></div>
                <div class="field field-wide"><label for="motivo">Motivo de la consulta</label><input id="motivo" name="motivo" value="{{ old('motivo', $ficha?->motivo) }}" required minlength="3" maxlength="255"><x-error field="motivo" /></div>
                @if ($ficha)
                    <div class="field"><label for="estado">Estado</label><select id="estado" name="estado" required>@foreach (['Pendiente', 'Cancelada'] as $estado)<option value="{{ $estado }}" @selected(old('estado', $ficha->estado) === $estado)>{{ $estado }}</option>@endforeach</select><x-error field="estado" /></div>
                @endif
            </div>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('fichas.index') }}">Cancelar</a><button class="button button-primary" type="submit" @disabled($pacientes->isEmpty())>{{ $ficha ? 'Guardar cambios' : 'Asignar ficha' }}</button></div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const fecha = document.getElementById('fecha');
    const hora = document.getElementById('hora');
    const doctor = document.getElementById('doctor_id');
    const mensaje = document.getElementById('disponibilidad');
    const seleccionado = doctor.dataset.selected;
    const endpoint = @json(route('fichas.doctores-disponibles'));
    const ignorar = @json($ficha?->id);

    async function actualizarDoctores() {
        if (!fecha.value || !hora.value) {
            mensaje.textContent = 'La disponibilidad se consulta al indicar fecha y hora.';
            return;
        }

        mensaje.textContent = 'Consultando disponibilidad...';
        doctor.disabled = true;
        try {
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('fecha', fecha.value);
            url.searchParams.set('hora', hora.value);
            if (ignorar) url.searchParams.set('ignorar', ignorar);
            const respuesta = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!respuesta.ok) throw new Error('No se pudo consultar la disponibilidad.');
            const disponibles = await respuesta.json();
            const anterior = doctor.value || seleccionado;
            doctor.replaceChildren(new Option('Selecciona un doctor', ''));
            disponibles.forEach((d) => doctor.add(new Option(`${d.nombre} — ${d.especialidad}`, d.id)));
            if (disponibles.some((d) => String(d.id) === String(anterior))) doctor.value = anterior;
            mensaje.textContent = disponibles.length
                ? `${disponibles.length} doctor(es) disponible(s) en este horario.`
                : 'No hay doctores disponibles en este horario. Prueba otra hora.';
        } catch (error) {
            mensaje.textContent = error.message;
        } finally {
            doctor.disabled = false;
        }
    }

    fecha.addEventListener('change', actualizarDoctores);
    hora.addEventListener('change', actualizarDoctores);
    if (fecha.value && hora.value) actualizarDoctores();
})();
</script>
@endpush
